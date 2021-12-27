<?php

namespace App\Models;

use App\Casts\DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantMeta extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference_id',
        'payment_gateway_id',
        'status',
        'is_able_to_accept_payments',
        'currently_due',
        'eventually_due',
        'disabled_reason',
        'primary_email_confirmed',
        'details_submitted',
    ];

    protected $casts = [
        'is_able_to_accept_payments' => 'boolean',
        'currently_due' => 'json',
        'eventually_due' => 'json',
        'requirements' => 'json',
        'created_at' => DateTime::class,
        'updated_at' => DateTime::class,
        'deleted_at' => DateTime::class,
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function paymentGateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id');
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function scopeByGateway($query, $gateway)
    {
        return $query->whereHas('gateway', function ($query) use ($gateway) {
            $query->where('key', $gateway);
        });
    }
}
