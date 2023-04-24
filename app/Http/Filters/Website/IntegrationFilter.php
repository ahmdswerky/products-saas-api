<?php

namespace App\Http\Filters\Website;

use App\Traits\QueryFilter;

class IntegrationFilter extends QueryFilter
{
    public function used()
    {
        // return $this->builder->where('is_used', true);
        return $this->builder->whereHas('integration', function ($query) {
            $query->where('is_used', true);
        });
    }

    public function available()
    {
        return $this->builder->where('is_available', true);
    }
}
