<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource as BaseJsonResource;
use Illuminate\Support\Arr;

class JsonResource extends BaseJsonResource
{
    public function extend($key, $resource, $fallback = false)
    {
        $extend = request()->query('extend');

        if ($fallback == false) {
            return $this->when($extend && in_array($key, $extend), $resource);
        }

        return $this->when($extend && in_array($key, $extend), $resource, $fallback);
    }

    public function queryHas($name, $value)
    {
        $extend = request()->query('extend');

        return $this->when($extend && array_key_exists($name, $extend), $value);
    }

    public function queryNotHas($name, $value)
    {
        $extend = request()->query('extend') ?: [];

        return $this->when(!array_key_exists($name, $extend), $value);
    }
}
