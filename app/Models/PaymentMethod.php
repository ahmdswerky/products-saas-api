<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'key',
        'payment_gateway_id',
    ];

    public function getRouteKeyName()
    {
        return 'key';
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id');
    }

    public static function byKey($key)
    {
        return optional(
            self::where('key', $key)->first()
        )->id;
    }
}
