<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class TransferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Transfer $this */
        return [
            'reference_number' => $this->reference_number,
            'amount' => $this->amount,
            'status' => $this->status,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'sender_account' => [
                'id' => $this->senderAccount->id,
                'account_number' => $this->senderAccount->account_number,
                'user' => [
                    'name' => $this->senderAccount->user->name,
                    'email' => $this->senderAccount->user->email,
                ],
            ],
            'recipient_account' => [
                'id' => $this->recipientAccount->id,
                'account_number' => $this->recipientAccount->account_number,
                'user' => [
                    'name' => $this->recipientAccount->user->name,
                    'email' => $this->recipientAccount->user->email,
                ],
            ],
        ];
    }
} 