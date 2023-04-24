<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'integration_name_id',
        'merchant_id',
        'key',
        'secret',
        'is_used',
    ];

    protected $casts = [
        'is_used' => 'bool',
    ];

    protected static function booted()
    {
        self::creating(function (self $integration) {
            $integration->is_used = false;
        });
    }

    public function integrationName()
    {
        return $this->belongsTo(IntegrationName::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
