<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    const USER_RESPONSE_PENDING = 0;
    const USER_RESPONSE_ACCEPTED = 1;
    const USER_RESPONSE_REJECTED = 2;

    use HasFactory, HasUuids;
    public $incrementing = false;
    protected $table = 'loans';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public function repayment_plan()
    {
        return $this->hasMany('App\Models\Repayment', 'loan_id' , 'id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'interest_rate',
        'amount',
        'duration',
        'start_date',
        'admin_id',
        'interest',
        'loan_request_id',
        'user_respond',
        'amount_paid',
        'interest_paid'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'float',
        'interest' => 'float',
        'amount_paid' => 'float',
        'interest_paid' => 'float',
        'interest_rate' => 'float'
    ];
}
