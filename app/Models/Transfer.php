<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Transfer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'sender_account_id',
        'recipient_account_id',
        'amount',
        'converted_amount',
        'currency',
        'recipient_currency',
        'description',
        'status',
        'reference_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'converted_amount' => 'decimal:2',
    ];

    public function senderAccount(): BelongsTo
    {
        return $this->belongsTo(SavingsAccount::class, 'sender_account_id');
    }

    public function recipientAccount(): BelongsTo
    {
        return $this->belongsTo(SavingsAccount::class, 'recipient_account_id');
    }
}
