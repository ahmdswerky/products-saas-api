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
        'product' => 'exists:products,public_id',
        'auth' => 'required_with_all:merchant,user',
        'merchant' => 'required_with_all:auth,user',
    ];

    public function product($id)
    {
        //$this->builder->where('product_id', $id);
        $this->builder->whereHas('product', fn ($product) => $product->where('public_id', $id));
    }

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
