<?php

namespace App\Models;

use App\Enums\WithdrawalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalRequest extends Model
{
    use HasFactory;

    protected $table = 'withdrawal_requests';

    protected $fillable = [
        'user_id',
        'points_amount',
        'cash_amount',
        'currency',
        'payment_method',
        'payment_account',
        'status',
        'transaction_reference',
        'processed_by',
    ];

    protected $casts = [
        'cash_amount' => 'decimal:2',
        'status' => WithdrawalStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
