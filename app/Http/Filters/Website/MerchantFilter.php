<?php

namespace App\Http\Filters\Website;

use App\Traits\QueryFilter;

class MerchantFilter extends QueryFilter
{
    public function status($status)
    {
        return $this->builder->where('status', $status);
    }
}
