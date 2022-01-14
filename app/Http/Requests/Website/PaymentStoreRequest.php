<?php

namespace App\Http\Requests\Website;

use App\Models\Currency;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Rules\Quantity;
use Illuminate\Validation\Rule;
use App\Services\PaymentService;
use Illuminate\Foundation\Http\FormRequest;

class PaymentStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_id' => 'bail|required|integer|exists:products,id',
            'method' => [
                'required',
                Rule::in(
                    PaymentMethod::select('key')
                        ->get()
                        ->pluck('key')
                        ->toArray(),
                ),
            ],
            'currency' => [
                'bail',
                'sometimes',
                'required',
                'size:3',
                Rule::in(
                    Currency::select('name')
                        ->get()
                        ->pluck('name')
                        ->toArray(),
                ),
            ],
            'quantity' => [
                new Quantity(optional($this->product)->quantity - 1)
            ],
        ];
    }

    public function prepareForValidation()
    {
        // TODO: fix
        //$this->product = Product::find($this->product_id);
        $this->product = Product::where('public_id', $this->product_id)->first();
        $gateway = PaymentService::gateway($this->input('method'));

        $this->merge([
            'account_id' => optional($this->product)->getAccountId($gateway),
            'product_id' => optional($this->product)->id,
        ]);

        optional($this->product)->getAccountId($gateway);
    }
}
