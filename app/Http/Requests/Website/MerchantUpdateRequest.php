<?php

namespace App\Http\Requests\Website;

use App\Models\Status;
use App\Models\PaymentMethod;
use App\Models\PaymentGateway;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class MerchantUpdateRequest extends FormRequest
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
            //'reference_id' => 'required',
            //'currently_due' => 'required|array',
            //'currently_due' => 'required|array',
            //'disabled_reason' => 'nullable',
            //'code' => 'required_if:gateway,stripe',
            //'method' => [
            //    'required',
            //    Rule::in(
            //        PaymentMethod::select('key')
            //            ->get()
            //            ->pluck('key')
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
            'status' =>  'required|in:disconnected',
        ];
    }
}
