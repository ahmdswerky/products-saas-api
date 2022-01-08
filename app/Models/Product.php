<?php

namespace App\Models;

use App\Casts\DateTime;
use App\Traits\Mediable;
use App\Traits\Filterable;
use Illuminate\Support\Str;
use App\Enums\PaymentStatus;
use App\Helpers\CurrencyConverter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, Filterable, Mediable, SoftDeletes;

    protected $fillable = [
        'public_id',
        'title',
        'slug',
        'description',
        'usd_price',
        'category',
        'price',
        'currency',
        'quantity',
        'merchant_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'integer',
        'created_at' => DateTime::class,
    ];

    //public function getRouteKeyName()
    //{
    //    return 'slug';
    //}

    public static function boot()
    {
        parent::boot();

        self::creating(function ($product) {
            $product->public_id = Str::random(20);
            $product->slug = $product->title;
            $product->usd_price = CurrencyConverter::convert($product->price, 'USD', $product->currency);
        });
    }

    public function setSlugAttribute($value)
    {
        if ($value) {
            $this->attributes['slug'] = slug($value, self::class, 'slug');
        }
    }

    public function getToalPaymentsAttribute()
    {
        return $this->payments()->sum('usd_amount');
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function successfulPayments(): HasMany
    {
        return $this->payments()->where('status', PaymentStatus::SUCCEEDED);
    }

    public function getMerchantsIdsAttribute()
    {
        $metas = MerchantMeta::with(['gateway', 'merchant'])->where('merchant_id', $this->id)->get();

        return $metas->map(function ($one) {
            return [
                'gateway' => $one->gateway->key,
            ];
        });
    }

    public function getAccountId($gateway)
    {
        $meta = MerchantMeta::where('merchant_id', $this->merchant_id)
            ->whereHas('gateway', function ($query) use ($gateway) {
                $query->where('key', $gateway);
            })
            ->first();

        return $meta->reference_id;
    }

    public function getAccountIdByMethod($method)
    {
        $meta = MerchantMeta::where('merchant_id', $this->merchant_id)
            ->whereHas('gateway', function ($query) use ($method) {
                $query->method($method);
            })
            ->first();

        return $meta->reference_id;
    }

    public function getQuantityAttribute($value)
    {
        $reserved = 0 ?: $this->successfulPayments()->count();
        $quantity = $value - $reserved;

        return $quantity > 0 ? $quantity : 0;
    }
}
