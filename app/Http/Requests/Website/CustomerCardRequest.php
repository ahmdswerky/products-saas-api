<?php

namespace App\Http\Requests\Website;

use App\Models\PaymentMethod;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CustomerCardRequest extends FormRequest
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
            'method' => [
                'required',
                Rule::in(
                    PaymentMethod::select('key')
                        ->get()
                        ->pluck('key')
                        ->toArray(),
                ),
            ],
            'token' => 'required',
        ];
    }
}
