<?php

namespace App\Http\Requests\Website;

use App\Models\PaymentGateway;
use App\Models\Status;
use App\Models\PaymentMethod;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class MerchantStoreRequest extends FormRequest
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
            'code' => 'required_if:method,credit_card',
            'remote_id' => 'required_if:method,paypal',
            'gateway' => [
                'required',
                Rule::in(
                    PaymentGateway::select('key')
                        ->get()
                        ->pluck('key')
                        ->toArray(),
                )
            ],
            //'method' => [
            //    'required',
            //    Rule::in(
            //        PaymentMethod::select('key')
            //            ->get()
            //            ->pluck('key')
            //            ->toArray(),
            //    )
            //],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            //'payment_method_id' => PaymentMethod::byKey($this->method),
            'payment_gateway_id' => PaymentGateway::byKey($this->gateway),
        ]);
    }
}
