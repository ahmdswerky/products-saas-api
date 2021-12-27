<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentGateway extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'key',
    ];

    public function methods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function merchants(): HasMany
    {
        return $this->hasMany(Merchant::class);
    }

    // TODO: complete
    public function scopeApproved($query)
    {
        return $query;
    }

    // TODO: complete
    public function scopeAvailable($query)
    {
        return $query;
    }

    public function scopeMethod($query, $method)
    {
        return $query->whereHas('methods', function ($query) use ($method) {
            $query->where('key', $method);
        });
    }

    public static function byMethodKey($key)
    {
        return PaymentMethod::byKey($key);
    }

    public static function byKey($key)
    {
        return optional(
            self::where('key', $key)->first()
        )->id;
    }

    public function merchantMetas(): HasMany
    {
        return $this->hasMany(MerchantMeta::class);
    }
}
