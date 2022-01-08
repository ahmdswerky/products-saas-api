<?php

namespace App\Http\Resources\Website;

use App\Http\Resources\JsonResource as BaseJsonResource;

class JsonResource extends BaseJsonResource
{
    public function id()
    {
        $key = $this->getRouteKeyName();

        return $this->public_id ?: $this->$key;
    }
}
