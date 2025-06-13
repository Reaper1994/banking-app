<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SavingsAccount;
use App\Models\Transfer;
use App\Repositories\TransferRepository;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class TransferService
{
    public function __construct(
        private readonly TransferRepository $transferRepository,
        private readonly CurrencyConversionService $currencyService
    ) {
    }

    public function initiateTransfer(
        SavingsAccount $senderAccount,
        SavingsAccount $recipientAccount,
        float $amount,
        ?string $description,
        string $currency
    ): Transfer {
        if ($senderAccount->id === $recipientAccount->id) {
            throw new InvalidArgumentException('Cannot transfer to the same account');
        }

        if ($senderAccount->balance < $amount) {
            throw new InvalidArgumentException('Insufficient funds');
        }

        //allowed multi currency transfer
        // if ($senderAccount->currency !== $currency) {
        //     throw new InvalidArgumentException('Sender account currency does not match transfer currency');
        // }

        return DB::transaction(function () use ($senderAccount, $recipientAccount, $amount, $description, $currency) {
            $convertedAmount = $this->currencyService->convert(
                $amount,
                $senderAccount->currency->code,
                $recipientAccount->currency->code
            );

            $senderAccount->decrement('balance', $amount);

            $recipientAccount->increment('balance', $convertedAmount);

            return $this->transferRepository->create([
                'sender_account_id' => $senderAccount->id,
                'recipient_account_id' => $recipientAccount->id,
                'amount' => $amount,
                'converted_amount' => $convertedAmount,
                'currency' => $currency,
                'recipient_currency' => $recipientAccount->currency->code,
                'description' => $description,
                'status' => 'completed',
                'reference_number' => $this->generateReferenceNumber(),
            ]);
        });
    }

    private function generateReferenceNumber(): string
    {
        return 'TRF-' . strtoupper(uniqid());
    }
}
