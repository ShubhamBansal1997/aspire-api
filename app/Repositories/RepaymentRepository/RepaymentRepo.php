<?php
namespace App\Repositories\RepaymentRepository;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

# Traits 
use App\Repositories\RepaymentRepository\CalculationTraits;

# Model
use App\Models\Repayment;
use App\Models\Loan;

# Interface
use App\Repositories\RepaymentRepository\RepaymentInterface;
use App\Repositories\LoanRepository\LoanInterface;

class RepaymentRepo implements RepaymentInterface
{
    use CalculationTraits;
    
    protected $loanRepo;

    public function __construct(LoanInterface $loanRepo)
    {
        $this->loanRepo = $loanRepo;
        $this->now = Carbon::now();
    }
    

    /* 
   |---------------------------------------
   | create new repayment plan (by admin)
   |---------------------------------------
   */
    public function createRepaymentPlan($loan_id)
    {
         if(!$this->hasRepaymentPlan($loan_id)){
            
            $get_new_plan = $this->getNewRepaymentPlan($loan_id);
            foreach($get_new_plan as $row)
            {
               $this->saveNewRepaymentPlan($loan_id, $row);
            }
            $this->updateInterest($loan_id);
            return $get_new_plan;
         }
         return null;
    }

    /** Update interest as per payment */
    protected function updateInterest($loan_id)
    {
        if($this->hasRepaymentPlan($loan_id)){
            $loan = $this->loanRepo->details($loan_id);
            $interest = 0;
            foreach($loan->repayment_plan as $row)
            {
                $interest += $row->interest;
            }
            $loan->update([
                'interest' => $interest
            ]);
        }     
    }

    /* check the loan have created repayment plan or not */
    protected function hasRepaymentPlan($loan_id)
    {
        return Repayment::where('loan_id', $loan_id)->exists();
    }

    /* based on loans attributes, the new repayment plan is calculated */
    protected function getNewRepaymentPlan($loan_id): array
    {
         $loan_details = $this->loanRepo->details($loan_id);
         if($loan_details) {
            return $this->getRepaymentPlan($loan_details->amount, $loan_details->interest_rate, $loan_details->duration, $loan_details->start_date, $loan_details->duration);
         }
    }
    
    /* Save the new repayment plan in database */
    protected function saveNewRepaymentPlan($loan_id, $data)
    {
         $new_data = array_merge($data, [ 'loan_id'  => $loan_id]);
         $create = Repayment::create($new_data);
         return $create;
    }

    /**
     * If the user paid extra interest then
     * update the repayment plan
     */
    protected function updateRepaymentPlan($loan_id)
    {
        if($this->hasRepaymentPlan($loan_id)) {
            $loan_details = $this->loanRepo->details($loan_id);
            $loan_data = $this->getRepaymentInfo($loan_details->repayment_plan);
            $amount_unpaid = $loan_details->amount - $loan_data['amount_paid'];
            if($loan_details) {
                $get_updated_plan =  $this->getRepaymentPlan($amount_unpaid, $loan_details->interest_rate, $loan_data['repayment_unpaid'], $loan_data['repayment_start_date'], $loan_details->duration, $loan_data['repayment_paid']);
                foreach($get_updated_plan as $row)
                {
                    $new_data = array_merge($row, [ 'loan_id'  => $loan_id]);
                    $repayment = Repayment::where('loan_id', $loan_id)->where('payment_no', $new_data['payment_no'])->update($new_data);
                }
                return $get_updated_plan;
            }
            $this->updateInterest($loan_id);

        }
        return null;
        
    }
    
   
   /* 
   |---------------------------------------
   | making payment for repayment (by user)
   |---------------------------------------
   */
    public function makePayment($loan_id, $data)
    {
         $upcoming_repayment = $this->getUpcomingPayment($loan_id);
         if($upcoming_repayment){
            $repayable_amount = intval($upcoming_repayment->repayable_amount);
            $repayable_amount_paid = intval($data['repayable_amount_paid']);
            //dd($data);
            $upcoming_repayment->update($data);
            $loan = $this->loanRepo->details($loan_id);
            $loan_data = $this->getRepaymentInfo($loan->repayment_plan);
            $loan->update([
                'amount_paid' => $loan_data['amount_paid'],
                'interest_paid' => $loan_data['interest_paid']
            ]);
            if ($repayable_amount_paid > $repayable_amount)
                $this->updateRepaymentPlan($loan_id);
            return ["success" => $upcoming_repayment];
        }
        return ["fail" => "Repayment Failed!"];
    }

    /* Check loan have any repayable payments pending */
    public function checkRepayableRecord($loan_id)
    {
         $upcoming_repayment = $this->getUpcomingPayment($loan_id);
         if($upcoming_repayment){
            return ['success' => $upcoming_repayment];
         }else{
            return ['error' => "no repayable records"];
         }
    }

    /* check user make correct payment amount */
    public function checkAmount($loan_id, $amount)
    {
        $upcoming_repayment = $this->getUpcomingPayment($loan_id);
        $repayable_amount = intval($upcoming_repayment->repayable_amount);
        $paid_amount = intval($amount);
        if($paid_amount == $repayable_amount){
            return ['success' => [
                                'msg' => Repayment::REPAYMENT_PAID_IN_FULL, 
                                'repayable amount' => $upcoming_repayment->repayable_amount, 
                                'paid amount' => $amount
                                ]
            ];

         }elseif($paid_amount < $repayable_amount){
            return ['error' => [
                              'msg' => Repayment::REPAYMENT_UNDER_PAID, 
                              'repayable amount' => $upcoming_repayment->repayable_amount, 
                              'paid amount' => $amount
                              ]
                  ];
            
         }elseif($paid_amount > $repayable_amount){
            return ['success' => [
                              'msg' => Repayment::REPAYMENT_OVER_PAID, 
                              'repayable amount' => $upcoming_repayment->repayable_amount, 
                              'paid amount' => $amount
                              ]
                  ];
         }
    }

    /* get latest up coming repayment records based on loan_id */
    public function getUpcomingPayment($loan_id)
    {
         return Repayment::where('loan_id', $loan_id)->where('is_paid', Repayment::PAYMENT_STATUS_UNPAID)->orderBy('payment_no')->first();
    }

    /* get all repayments records that have NOT been paid yet by the loan */
     public function getOutstandingByLoanId($loan_id)
     {
        return Repayment::where('loan_id', $loan_id)->where('is_paid', Repayment::PAYMENT_STATUS_UNPAID)->get();
     }

     /* get all repayments records that have NOT been paid yet by the user */
    public function getOutstandingByUserId($user_id)
    { 
        $outstanding = [];
        $user_loans = $this->loanRepo->getLoansByUserId($user_id);
        foreach($user_loans as $loan){
            $outstanding []  = $this->getOutstandingByLoanId($loan->id);
        }
        return array_flatten($outstanding);
    }
}
