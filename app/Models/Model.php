<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    public function getRouteKeyName()
    {
        //if ($this->slug) {
        //    return 'slug';
        //}

        if (active_guard() === 'key') {
            return 'public_id' ?: 'id';
        }
    }
}
