<?php
namespace App\Repositories\LoanRepository;

use Illuminate\Http\Request;
use App\Http\Requests;

# Model
use App\Models\Loan;
use App\Models\LoanRequest;

# Interface
use App\Repositories\LoanRepository\LoanInterface;

class LoanRepo implements LoanInterface
{
    

    public function __construct()
    {
        
    }

    public function createLoanRequest($data, $uid)
    {    
         $new_data = array_merge($data, [ 'user_id'  => $uid]);

         return LoanRequest::create($new_data);
    }

    public function getRequestedLoans($uid)
    {
         return LoanRequest::where('user_id', $uid)->get();
    }
   

   /**********************
    * START: loan 
    **********************/

    public function createLoan($data)
    {   
        return Loan::create($data);
    }

    public function respondOffer($loan_id, $user_respond)
    { 
        $loan = $this->details($loan_id);
        if($loan){
            $loan->update([
                'user_respond' => $user_respond
            ]);
        }
        return $loan;

    }

    public function isOfferedPerson($user_id, $loan_id)
    {
         $check = Loan::where('user_id', $user_id)->where('id', $loan_id)->exists();
         return $check 
               ? ['success' => $check] 
               : ['error' => 'unauthorized access to loan'];
    }

    public function details($loan_id)
    {
        $loan = Loan::with('repayment_plan')->find($loan_id);
        return $loan;
    }

    /* get all loans that offered to the user */
    public function getLoansByUserId($user_id)
    {
        $loans = Loan::with('repayment_plan')->where('user_id', $user_id)->get();
        return $loans;
    }

    public function isLoanAccepted($loan_id)
    {
        $check = Loan::where('id', $loan_id)->where('user_respond', Loan::USER_RESPONSE_ACCEPTED)->exists();
         return $check 
               ? ['success' => $check] 
               : ['error' => 'Loan is not accepted'];
    }

    public function isLoanOfferResponded($loan_id)
    {
        $check = Loan::where('id', $loan_id)->where('user_respond', Loan::USER_RESPONSE_PENDING)->exists();
         return !$check 
               ? ['success' => $check] 
               : ['error' => 'Loan is not accepted'];
    }



}