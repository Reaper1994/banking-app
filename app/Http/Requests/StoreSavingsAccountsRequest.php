<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\SavingsAccount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'accounts' => ['required', 'array', 'min:1'],
            'accounts.*.user_id' => ['required', 'exists:users,id'],
            'accounts.*.first_name' => ['required', 'string', 'max:255'],
            'accounts.*.last_name' => ['required', 'string', 'max:255'],
            'accounts.*.date_of_birth' => ['required', 'date', 'before:today'],
            'accounts.*.address' => ['required', 'string', 'max:255'],
            'accounts.*.currency_id' => [
                'required',
                'exists:currencies,id',
                Rule::exists('currencies', 'id')->where('is_active', true),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'accounts.required' => 'At least one account must be created.',
            'accounts.min' => 'At least one account must be created.',
            'accounts.*.user_id.required' => 'User selection is required.',
            'accounts.*.user_id.exists' => 'Selected user does not exist.',
            'accounts.*.first_name.required' => 'First name is required.',
            'accounts.*.last_name.required' => 'Last name is required.',
            'accounts.*.date_of_birth.required' => 'Date of birth is required.',
            'accounts.*.date_of_birth.date' => 'Invalid date format.',
            'accounts.*.date_of_birth.before' => 'Date of birth must be before today.',
            'accounts.*.address.required' => 'Address is required.',
            'accounts.*.currency_id.required' => 'Currency selection is required.',
            'accounts.*.currency_id.exists' => 'Selected currency does not exist or is not active.',
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
