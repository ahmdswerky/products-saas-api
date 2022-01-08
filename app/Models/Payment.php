<?php

namespace App\Models;

use App\Casts\DateTime;
use App\Traits\Filterable;
use Illuminate\Support\Str;
use App\Enums\PaymentStatus;
use App\Traits\Referenceable;
use App\Helpers\CurrencyConverter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, Filterable, Referenceable, SoftDeletes;

    public $fillable = [
        'public_id',
        'usd_amount',
        'amount',
        'currency',
        'product_id',
        'payment_method_id',
        'status',
    ];

    protected $casts = [
        'amount' => 'integer',
        'created_at' => DateTime::class,
        'updated_at' => DateTime::class,
        'deleted_at' => DateTime::class,
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($payment) {
            $payment->public_id = Str::random(20);
            $payment->usd_amount = CurrencyConverter::convert($payment->amount, 'USD', $payment->currency);
        });
    }

    public function scopeByMerchant($query, $merchantId)
    {
        return $query->whereHas('product', function ($query) use ($merchantId) {
            $query->where('merchant_id', $merchantId);
        });
    }

    public function scopeSucessful($query)
    {
        return $query->where('status', PaymentStatus::SUCCEEDED);
    }

    //public function merchant(): Builder
    public function merchant(): BelongsTo
    {
        return $this->product->merchant();

        //$gatewayId = PaymentMethod::find($this->payment_method_id)->payment_gateway_id;

        //return $this->product
        //    ->merchants()
        //    ->where('payment_gateway_id', $gatewayId);
    }

    //public function getMerchantAttribute(): Merchant
    //{
    //    return $this->merchant()->first();
    //}

    public function getAccountIdbyMethod($method)
    {
        return MerchantMeta::whereHas('merchant', function ($query) {
            $query->where('merchant_id', $this->merchant()->select('id')->first()->id);
        });
    }

    public function gateway(): BelongsTo
    {
        return $this->method->gateway();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function method(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
