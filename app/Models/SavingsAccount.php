<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavingsAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_number',
        'user_id',
        'balance',
        'currency_id',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($account) {
            if (empty($account->account_number)) {
                $account->account_number = static::generateUniqueAccountNumber();
            }
        });
    }

    protected static function generateUniqueAccountNumber(): string
    {
        do {
            $number = 'SA' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        } while (static::where('account_number', $number)->exists());

        return $number;
    }
}
