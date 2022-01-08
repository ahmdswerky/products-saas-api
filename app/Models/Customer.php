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
        'public_id',
        'reference_id',
        'payment_gateway_id',
        'user_id',
    ];

    public function ownedBy(string|int $userId): bool
    {
        return (int) $this->user_id === $userId;
    }

    public function scopeByMethod($query, string $method)
    {
        return $query->where(function ($query) use ($method) {
            $query->whereHas('gateway', function ($query) use ($method) {
                $query->whereHas('methods', function ($query) use ($method) {
                    $query->where('key', $method);
                });
            });
        });
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
