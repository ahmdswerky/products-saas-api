<?php

namespace App\Models;

use App\Traits\Mediable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Mediable, HasFactory, Notifiable;

    protected $fillable = [
        'public_id',
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($user) {
            $user->public_id = Str::random(20);
        });
    }

    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    public function setPasswordAttribute($value)
    {
        if (!is_null($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function merchant(): HasOne
    {
        return $this->hasOne(Merchant::class);
    }

    public function merchants(): HasMany
    {
        return $this->hasMany(Merchant::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getAvatarAttribute()
    {
        return $this->photo('avatar')->first();
    }
}
