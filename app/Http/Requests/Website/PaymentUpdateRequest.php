<?php

namespace App\Http\Requests\Website;

use App\Enums\PaymentStatus;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PaymentUpdateRequest extends FormRequest
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
            //'status' => [
            //    'required',
            //    Rule::in([
            //        PaymentStatus::CANCELED,
            //        PaymentStatus::SUCCEEDED,
            //    ]),
            //],
        ];
    }
}
