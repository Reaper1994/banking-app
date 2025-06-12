<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\SavingsAccount;

class StoreSavingsAccountsRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
     public function rules(): array
    {
        return [
            'accounts' => 'required|array|min:1',
            'accounts.*.user_id' => 'required|exists:users,id',
            'accounts.*.first_name' => 'required|string|max:255',
            'accounts.*.last_name' => 'required|string|max:255',
            'accounts.*.date_of_birth' => 'required|date',
            'accounts.*.address' => 'required|string|max:255',
            'accounts.*.currency_id' => 'required|exists:currencies,id',
        ];
    }

    public function messages(): array
    {
        return [
            'accounts.required' => 'You must provide at least one account.',
            'accounts.array' => 'Accounts must be an array.',
            'accounts.min' => 'At least one account is required.',
            
            'accounts.*.user_id.required' => 'Each account must have a user ID.',
            'accounts.*.user_id.exists' => 'One or more user IDs are invalid.',
            
            'accounts.*.first_name.required' => 'First name is required for each account.',
            'accounts.*.first_name.string' => 'First name must be a string.',
            'accounts.*.first_name.max' => 'First name may not exceed 255 characters.',

            'accounts.*.last_name.required' => 'Last name is required for each account.',
            'accounts.*.last_name.string' => 'Last name must be a string.',
            'accounts.*.last_name.max' => 'Last name may not exceed 255 characters.',

            'accounts.*.date_of_birth.required' => 'Date of birth is required.',
            'accounts.*.date_of_birth.date' => 'Date of birth must be a valid date.',

            'accounts.*.address.required' => 'Address is required for each account.',
            'accounts.*.address.string' => 'Address must be a string.',
            'accounts.*.address.max' => 'Address may not exceed 255 characters.',

            'accounts.*.currency_id.required' => 'Currency is required.',
            'accounts.*.currency_id.exists' => 'One or more currency IDs are invalid.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            foreach ($this->accounts as $index => $account) {
                $existing = SavingsAccount::where('user_id', $account['user_id'])
                    ->where('currency_id', $account['currency_id'])
                    ->exists();

                if ($existing) {
                    $validator->errors()->add(
                        "accounts.$index.currency_id",
                        "This user already has a savings account with the selected currency."
                    );
                }
            }
        });
    }
}
