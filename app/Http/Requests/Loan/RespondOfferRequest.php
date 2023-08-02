<?php

namespace App\Http\Requests\Loan;

# Model
use App\Models\Loan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RespondOfferRequest extends FormRequest
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
            'loan_id' => 'required', //loan id
            'user_respond' => 'required|numeric|in:'.Loan::USER_RESPONSE_ACCEPTED.','.Loan::USER_RESPONSE_REJECTED, //1 or 2 only
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'loan_id.required' => 'loan id is required!',
            'user_respond.required' => 'user respond is required',
            'user_respond.numeric' => 'user respond must be numeric!',
            'user_respond.in' => 'accept only either 1 or 2',
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
