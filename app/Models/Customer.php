<?php

namespace App\Models;

use App\Contracts\OwnershipContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    public $fillable = [
        'reference_id',
        'payment_gateway_id',
        'user_id',
    ];

    public function ownedBy(string|int $userId): bool
    {
        return (int) $this->user_id === $userId;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id');
    }
}
