<?php 
namespace App\Repositories\RepaymentRepository;

use Carbon\Carbon;
# Model
use App\Models\Repayment;

trait CalculationTraits
{
    public function getRepaymentInfo($repayments): array
    {
        $amount_paid = 0;
        $interest_paid = 0;
        $repayment_paid = 0;
        $repayment_unpaid = 0;

        $unpaid_repayment_dates = [];
        foreach($repayments as $repayment) {
            if ($repayment->is_paid == Repayment::PAYMENT_STATUS_PAID) {
                $amount_paid += $repayment->amount_paid;
                $interest_paid += $repayment->interest_paid;
                $repayment_paid++;
            } else {
                $repayment_unpaid++;
                $unpaid_repayment_dates[] = Carbon::parse($repayment->due_date);
            }
        }
        return [
            'amount_paid' => $amount_paid, 
            'interest_paid' => $interest_paid, 
            'repayment_paid' => $repayment_paid,
            'repayment_unpaid' => $repayment_unpaid,
            'repayment_start_date' => min($unpaid_repayment_dates)
        ];
    }
    public function getWeeklyInterest($amount, $rate): float
    {
        return $amount * $rate;
    }

    public function getWeeklyInterestRate($rate): float
    {
        return (0.01 * $rate)/52.1429; // Assuming 52 weeks in a year
    }

    protected function getRepaymentPlan($amount, $interest_rate, $duration, $start_date, $end_duration, $start_duration=0)
    {
         $repaymentPlan = [];
         $date = Carbon::parse($start_date);

         //weekly interest rate
         $weekly_interest_rate = $this->getWeeklyInterestRate($interest_rate);
         //dd($weekly_interest_rate);

         //weekly emi value
         $emi = $amount * $weekly_interest_rate * (1 + $weekly_interest_rate) ** $duration;
         if ($emi > 0)
            $emi /= ((1 + $weekly_interest_rate) ** $duration - 1);
         else
            $emi = (float)$amount/$duration;
         $emi = number_format($emi, 2, '.', '');
         $outstanding_amount = $amount;
         for ($n = $start_duration + 1; $n <= $end_duration; $n++) {
            $interest_component = $this->getWeeklyInterest($outstanding_amount, $weekly_interest_rate);
            $interest_component = number_format($interest_component, 2, '.', '');
            $principal_component = $emi - $interest_component;
            $repaymentPlan[$n] = 
                        [  
                           'payment_no'=> $n, 
                           'repayable_amount'=> $emi,
                           'amount'=> $principal_component,
                           'interest'=> $interest_component,
                           'weekly_interest_rate' => number_format($weekly_interest_rate, 4, '.', ''),
                           'due_date' => $date->addDays(7)->format('Y-m-d'),
                        ];
            $outstanding_amount -= $principal_component;
         }
         return $repaymentPlan;
    }
}