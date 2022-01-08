<?php

namespace App\Models;

use App\Casts\DateTime;
use App\Traits\Mediable;
use App\Traits\Filterable;
use Illuminate\Support\Str;
use App\Contracts\OwnershipContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Merchant extends Model
{
    use HasFactory, Mediable, Filterable, SoftDeletes;

    public $fillable = [
        'public_id',
        'api_key',
        'api_secret',
        'title',
        'user_id',
        'status',
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

    public static function boot()
    {
        parent::boot();

        self::creating(function ($merchant) {
            $merchant->api_key = Str::random(15);
            $merchant->api_secret = Str::random(40);
            $merchant->public_id = Str::random(20);
        });

        self::created(function ($merchant) {
            $gateways = PaymentGateway::approved()->select('id')->get();
            $gateways->map(function ($gateway) use ($merchant) {
                $merchant->metas()->create([
                    'payment_gateway_id' => $gateway->id,
                ]);
            });
        });
    }

    public function ownedBy(string|int $userId): bool
    {
        return (int) $this->user_id === $userId;
    }

    // TODO: complete
    //public function scopeByGateway($query, $gateway)
    //{
    //    return $query->whereHas('gateway', function ($query) use ($gateway) {
    //        $query->where('key', $gateway);
    //    });
    //}

    public function scopeByApiKey($query, $apiKey)
    {
        return $query->where('api_key', $apiKey);
    }

    public function scopeApproved($query)
    {
        // TODO: complete
        return $query;

        return $query->where('is_able_to_accept_payments', true)
            ->where('currently_due', json_encode([]))
            ->where('reference_id', '!=', null)
            ->whereHas('gateway', function ($query) {
                $query->approved();
            });
    }

    public function metas(): HasMany
    {
        return $this->hasMany(MerchantMeta::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //public function gateway(): BelongsTo
    //{
    //    return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id');
    //}

    public function getUserNameAttribute()
    {
        return $this->user->name;
    }

    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    public function getAvatarAttribute()
    {
        return $this->photo('avatar')->first();
    }
}
