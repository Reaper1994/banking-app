<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SavingsAccount;
use App\Models\Transfer;
use App\Repositories\Interfaces\TransferRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class TransferService
{
    public function __construct(
        private readonly TransferRepositoryInterface $transferRepository
    ) {
    }

    public function initiateTransfer(
        SavingsAccount $senderAccount,
        SavingsAccount $recipientAccount,
        float $amount,
        ?string $description = null
    ): Transfer {
        if ($senderAccount->id === $recipientAccount->id) {
            throw new InvalidArgumentException('Cant transfer to the same account');
        }

        if ($senderAccount->balance < $amount) {
            throw new InvalidArgumentException('Insufficient funds');
        }

        // Commented as we need multi currncy support
        // if ($senderAccount->currency_id !== $recipientAccount->currency_id) {
        //     throw new InvalidArgumentException('Currency mismatch between accounts');
        // }

        return DB::transaction(function () use ($senderAccount, $recipientAccount, $amount, $description) {
            $transfer = $this->transferRepository->create([
                'reference_number' => $this->generateReferenceNumber(),
                'sender_account_id' => $senderAccount->id,
                'recipient_account_id' => $recipientAccount->id,
                'amount' => $amount,
                'status' => 'pending',
                'description' => $description,
            ]);


            $senderAccount->decrement('balance', $amount);
            $recipientAccount->increment('balance', $amount);

            $this->transferRepository->updateStatus($transfer, 'completed');

            return $transfer;
        });
    }

    private function generateReferenceNumber(): string
    {
        do {
            $reference = 'TRF-' . strtoupper(Str::random(10));
        } while ($this->transferRepository->findByReferenceNumber($reference));

        return $reference;
    }
} 