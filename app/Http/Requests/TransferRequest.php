<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

final class TransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sender_account_id' => ['required', 'exists:savings_accounts,id'],
            'recipient_account_number' => ['required', 'exists:savings_accounts,account_number'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:255'],
            'currency' => ['required', 'string', 'size:3', 'in:USD,EUR,GBP'],
        ];
    }

    public function messages(): array
    {
        return [
            'sender_account_id.required' => 'The sender account ID is required.',
            'sender_account_id.exists' => 'The selected sender account does not exist.',
            'recipient_account_number.required' => 'The recipient account number is required.',
            'recipient_account_number.exists' => 'The selected recipient account does not exist.',
            'amount.required' => 'The amount is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 0.01.',
            'description.max' => 'The description cannot exceed 255 characters.',
            'currency.required' => 'The currency is required.',
            'currency.string' => 'The currency must be a string.',
            'currency.size' => 'The currency must be a 3-letter code.',
            'currency.in' => 'The currency must be USD, EUR, or GBP.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
