<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use Filterable;

    protected $fillable = [
        'mediable_type',
        'mediable_id',
        'type',
        'path',
        'name',
        'title',
        'group',
        'driver',
        'description',
        'is_main',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function scopeGroup($query, $groups)
    {
        return $query->whereIn('group', $groups);
    }

    public function mediable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDisplayedPathAttribute() : ?string
    {
        if (is_null($this->path)) {
            return $this->path;
        }

        if (Str::startsWith($this->path, 'http')) {
            return $this->path;
        }

        return url(
            str_replace('public', 'storage', $this->path) . '?' . Carbon::parse($this->updated_at)->getTimestamp(),
            [],
            !app()->isLocal()
        );
    }
}
