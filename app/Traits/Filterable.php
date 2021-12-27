<?php

namespace App\Traits;

trait Filterable
{
    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
