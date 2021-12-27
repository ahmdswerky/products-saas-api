<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reference extends Model
{
    use SoftDeletes;

    public $fillable = [
        'reference_id',
        'referencable_id',
        'referencable_type',
        'type',
    ];

    public function referencable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeByReference($query, $type, $id)
    {
        return $query->where('type', $type)->where('reference_id', $id);
    }

    public function gateway(): BelongsTo
    {
        return $this->referencable->gateway();
    }
}
