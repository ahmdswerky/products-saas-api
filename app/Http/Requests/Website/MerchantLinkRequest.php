<?php

namespace App\Http\Requests\Website;

use App\Models\PaymentMethod;
use App\Models\PaymentGateway;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class MerchantLinkRequest extends FormRequest
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
            //'merchant_slug' => [
            //    'required',
            //    //'exists:merchants,slug',
            //    Rule::in(
            //        Auth::user()->merchants()
            //            ->select('slug')
            //            ->get()
            //            ->pluck('slug')
            //            ->toArray(),
            //    )
            //],
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
            //            ->toArray()
            //    )
            //],
        ];
    }
}
