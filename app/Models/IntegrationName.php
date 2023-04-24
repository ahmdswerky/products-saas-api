<?php

namespace App\Models;

use App\Casts\DateTime;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IntegrationName extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'name',
        'description',
        'category',
        'url',
        'icon',
        'slug',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'bool',
        'created_at' => DateTime::class,
    ];

    protected static function booted()
    {
        static::creating(function ($integration) {
            $integration->slug = slug($integration->name, self::class, 'slug');
        });
    }

    public function integration()
    {
        return $this->hasOne(Integration::class);
    }

    public function integrations()
    {
        return $this->hasMany(Integration::class);
    }
}
