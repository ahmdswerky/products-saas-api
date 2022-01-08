<?php

namespace App\Http\Filters\Website;

use App\Traits\QueryFilter;

class ProductFilter extends QueryFilter
{
    public function search($query)
    {
        if (!strlen($query)) {
            return $this->builder;
        }

        return $this->builder->where('title', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%");
    }
}
