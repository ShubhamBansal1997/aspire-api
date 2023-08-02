<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

# Models
use App\Models\Repayment;

#Validation Requests
use App\Http\Requests\Loan\MakePaymentRequest;

# Interface
use App\Repositories\RepaymentRepository\RepaymentInterface;
use App\Repositories\LoanRepository\LoanInterface;

class RepaymentController extends Controller
{
    protected $repayRepo;
    protected $loanRepo;

    public function __construct(RepaymentInterface $repayRepo, LoanInterface $loanRepo)
    {
        $this->repayRepo = $repayRepo;
        $this->loanRepo = $loanRepo;
    }

    /**
     * make a payment by user/client
     * 
     * @param str $loan_id 
     * @param int $amount 
     * @param string $payment_method 
     * 
     * * @return repayment (JSON)
     */
    public function makePayment(MakePaymentRequest $request)
    {
        /* validation for authorized loan payment */
        $user_id = Auth::id();
        $check_authorized_loan = $this->loanRepo->isOfferedPerson($user_id, $request->loan_id);
        if(isset($check_authorized_loan['error'])){
            return response()->json($check_authorized_loan);
        }

        /* validation for loan acceptance */
        $loan_accepted = $this->loanRepo->isLoanAccepted($request->loan_id);
        if(isset($loan_accepted['error'])){
            return response()->json($loan_accepted);
        }

        /* validation for payment amount & repayable amount are same */
        $check_amount = $this->repayRepo->checkAmount($request->loan_id, $request->amount);
        if(isset($check_amount['error'])){
            return response()->json($check_amount);
        }

        /* validation for any repayable records */
        $check_repayable_record = $this->repayRepo->checkRepayableRecord($request->loan_id);
        if(isset($check_repayable_record['error'])){
            return response()->json($check_repayable_record);
        }
        //dd($check_repayable_record);
        $repayable_record = $check_repayable_record['success'];
        $amount_paid = $repayable_record->amount;
        if($check_amount['success']['msg'] == Repayment::REPAYMENT_OVER_PAID) {
            $amount_paid = $request->amount - $repayable_record->interest;
        }
        
        $data = 
            [
                'amount_paid' => $amount_paid,
                'interest_paid' => $repayable_record->interest,
                'repayable_amount_paid' => $request->amount,
                'paid_at' => Carbon::now()->format('Y-m-d'),
                'payment_method' => $request->payment_method,
                'is_paid' => Repayment::PAYMENT_STATUS_PAID
            ];
        //dd($data);

        return $this->repayRepo->makePayment($request->loan_id, $data);
    }
}
