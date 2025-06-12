<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Transfer;
use App\Repositories\Interfaces\TransferRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class TransferRepository implements TransferRepositoryInterface
{
    public function create(array $data): Transfer
    {
        return Transfer::create($data);
    }

    public function findByReferenceNumber(string $referenceNumber): ?Transfer
    {
        return Transfer::where('reference_number', $referenceNumber)->first();
    }

    public function getTransfersByAccount(int $accountId, int $perPage = 10): LengthAwarePaginator
    {
        return Transfer::where('sender_account_id', $accountId)
            ->orWhere('recipient_account_id', $accountId)
            ->with(['senderAccount.user', 'recipientAccount.user'])
            ->latest()
            ->paginate($perPage);
    }

    public function updateStatus(Transfer $transfer, string $status): bool
    {
        return $transfer->update(['status' => $status]);
    }
} 