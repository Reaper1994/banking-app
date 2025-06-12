<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Transfer;
use Illuminate\Pagination\LengthAwarePaginator;

interface TransferRepositoryInterface
{
    public function create(array $data): Transfer;
    public function findByReferenceNumber(string $referenceNumber): ?Transfer;
    public function getTransfersByAccount(int $accountId, int $perPage = 10): LengthAwarePaginator;
    public function updateStatus(Transfer $transfer, string $status): bool;
} 