<?php

namespace App\Http\Filters\Website;

use App\Models\Payment;
use App\Traits\QueryFilter;
use Illuminate\Support\Facades\Auth;

class PaymentFilter extends QueryFilter
{
    /** @var \Illuminate\Database\Eloquent\Model */
    protected $model = Payment::class;

    /** @var array */
    protected $validationRules = [
        'auth' => 'required_with_all:merchant,user',
        'merchant' => 'required_with_all:auth,user',
    ];

    public function auth()
    {
        $this->user(Auth::id());
    }

    public function user($userId)
    {
        $this->builder->where('user_id', $userId);
    }

    public function merchant($merchantId)
    {
        $this->builder->byMerchant($merchantId);
    }
}