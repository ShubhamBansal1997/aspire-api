<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    const PAYMENT_STATUS_UNPAID = 0;
    const PAYMENT_STATUS_PAID = 1;

    const REPAYMENT_PAID_IN_FULL = 0;
    const REPAYMENT_OVER_PAID = 1;
    const REPAYMENT_UNDER_PAID = 2;


    use HasFactory, HasUuids;
    public $incrementing = false;
    protected $table = 'repayments';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public function loan()
    {
        return $this->belongsTo('App\Models\Loan', 'loan_id', 'id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_no',
        'repayable_amount',
        'amount',
        'interest',
        'weekly_interest_rate',
        'due_date',
        'loan_id',
        'amount_paid',
        'interest_paid',
        'repayable_amount_paid',
        'paid_at',
        'payment_method',
        'is_paid',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'repayable_amount' => 'float',
        'repayable_amount_paid' => 'float',
        'amount' => 'float',
        'interest' => 'float',
        'amount_paid' => 'float',
        'interest_paid' => 'float',
        'weekly_interest_rate' => 'float',
    ];

    
}
