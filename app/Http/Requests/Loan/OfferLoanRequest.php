<?php

namespace App\Http\Requests\Loan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class OfferLoanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id'=> 'required|exists:users,id',
            'amount' => 'required|numeric',
            'interest_rate'=> 'required|numeric',
            'duration' => 'required|numeric',
            'start_date' => 'required|date_format:"Y-m-d"',
            'loan_request_id' => 'exists:loan_requests,id'
        ];
    }

    /**
     * Custom message for validation
     * 
     * @return array
     */
    public function messages(): array
    {
        return [
            'loan_request_id.exists' => 'loan request not exists in system',
            'user_id.exists' => 'user id not exists in system',
            'user_id.required' => 'user_id is required!',
            'amount.required' => 'loan amount is required!',
            'amount.numeric' => 'loan amount must be numeric!',
            'interest_rate.required' => 'interest rate is required!',
            'interest_rate.numeric' => 'interest rate must be numeric!',
            'duration.required' => 'duration of month is required!',
            'duration.numeric' => 'duration of month must be numeric!',
            'start_date.required' => 'please specify the intended date of the loan to start activate',
            'start_date.date_format' => 'date format should be Y-m-d '
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
