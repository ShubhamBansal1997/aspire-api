<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

#Validation Requests
use App\Http\Requests\Loan\RequestLoanRequest;

# Interface
use App\Repositories\LoanRepository\LoanInterface;
use App\Repositories\RepaymentRepository\RepaymentInterface;

class LoanRequestController extends Controller
{

    public function __construct(LoanInterface $loanRepo, RepaymentInterface $repayRepo) {
        $this->loanRepo = $loanRepo;
        $this->repayRepo = $repayRepo;
    }
    /**
     * request new loan by the user
     * 
     * @param int $amount
     * @param int $duration
     * @return loan (JSON)
     */
    public function create(RequestLoanRequest $request)
    {
        $request_loan = $this->loanRepo->createLoanRequest($request->all(), Auth::id());
        return $request_loan;
    }

    /**
     * get all requested loans by this authorized user
     * 
     * @return loan (JSON)
     */
    public function all()
    {
        $get_requested_loans = $this->loanRepo->getRequestedLoans(Auth::id());
        return $get_requested_loans;
    }
}
