<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\TransactionHistory;
use App\Models\Transfer;

final class TransferObserver
{
    public function created(Transfer $transfer): void
    {
        TransactionHistory::create([
            'savings_account_id' => $transfer->sender_account_id,
            'transfer_id' => $transfer->id,
            'type' => 'debit',
            'amount' => $transfer->amount,
            'currency' => $transfer->currency,
            'balance_before' => $transfer->senderAccount->balance + $transfer->amount,
            'balance_after' => $transfer->senderAccount->balance,
            'description' => $transfer->description,
        ]);

        TransactionHistory::create([
            'savings_account_id' => $transfer->recipient_account_id,
            'transfer_id' => $transfer->id,
            'type' => 'credit',
            'amount' => $transfer->converted_amount,
            'currency' => $transfer->recipient_currency,
            'balance_before' => $transfer->recipientAccount->balance - $transfer->converted_amount,
            'balance_after' => $transfer->recipientAccount->balance,
            'description' => $transfer->description,
        ]);
    }
}
