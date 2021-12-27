<?php

namespace App\Http\Requests\Website;

use App\Models\Merchant;
use App\Helpers\CurrencyConverter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->merchant()->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|min:2',
            //'usd_price' => 'bail|required|integer|min:1',
            'price' => 'bail|required|integer|min:1',
            'category' => 'required|min:2',
            'currency' => 'nullable|size:3|exists:currencies,name',
            'photo' => 'bail|required|image|max:4096',
            'merchant_id' => 'required|exists:merchants,id',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('currency')) {
            $this->merge([
                'currency' => strtoupper($this->currency),
            ]);
        }

        $this->merge([
            'merchant_id' => optional(Merchant::where('api_key', $this->header('api-key'))->select('id')->first())->id,
        ]);
    }
}
