<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


# Validation Requests
use App\Http\Requests\Loan\OfferLoanRequest;
use App\Http\Requests\Loan\RespondOfferRequest;

use App\Http\Controllers\API\RepaymentController;

# Interface
use App\Repositories\LoanRepository\LoanInterface;
use App\Repositories\RepaymentRepository\RepaymentInterface;


class LoanController extends Controller
{
    protected $loanRepo;
    protected $repayRepo;

    public function __construct(LoanInterface $loanRepo, RepaymentInterface $repayRepo)
    {
        $this->loanRepo = $loanRepo;
        $this->repayRepo = $repayRepo;
    }

    /**
     * Create new loan offer
     * 
     * @param str $user_id
     * @param float $amount
     * @param float $interest_rate
     * @param float $duration
     * @param int $repayment_frequency
     * @param str $admin_id
     * 
     * @return loan (JSON)
     */
    public function offerLoan(OfferLoanRequest $request)
    {
        if(Auth::user()->is_officer) {
            $request->request->add(['admin_id' => Auth::id()]);
            $new_loan = $this->loanRepo->createLoan($request->request->all());
            if($new_loan) {
                $repayment_plan = $this->repayRepo->createRepaymentPlan($new_loan->id);

            }
            $loan_details = $this->loanRepo->details($new_loan->id);
            return $loan_details;
        }
        return response()->json(['error' => "Not Authorized to do this action"], 401);
    }

    /**
     * View loan details & its re-payment information (by admin)
     * 
     * @param str $loan_id 
     * 
     * * @return loan (JSON)
     */
    public function details($loan_id)
    {
        if(Auth::user()->is_officer) {
            $loan_details = $this->loanRepo->details($loan_id);
            return $loan_details;
        }
        return response()->json(['error' => "Not Authorized to do this action"], 401);
    }

     /**
     * view all loans offered to the authorized user
     *
     * * @return loan (Json)
     */
    public function viewOfferedLoans()
    {
        $loans = $this->loanRepo->getLoansByUserId(Auth::id());
        return $loans;
    }

    /**
     * Respond to the loan offer which was offered to the user 
     * 
     * @param str $id (loan id)
     * @param string $user_respond (1 "accepted" or 2 "rejected")
     * 
     * @return loan (JSON)
     */
    public function respondOffer(RespondOfferRequest $request)
    {
        /* validation for authorized loan access */
        $isOffered = $this->loanRepo->isOfferedPerson(Auth::id(), $request->loan_id);
        if(isset($isOffered['success'])){
            $isOfferResponded = $this->loanRepo->isLoanOfferResponded($request->loan_id);
            if(!isset($isOfferResponded['success'])){
                $respond_loan = $this->loanRepo->respondOffer($request->loan_id, $request->user_respond);
                return $respond_loan;
            }
            return response()->json($isOfferResponded);
        }
        return response()->json($isOffered);
    }
}
