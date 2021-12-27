<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $userId = optional(optional($this->product)->merchant)->user_id;

        return Auth::id() === $userId;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'sometimes|required|min:2',
            'price' => 'sometimes|required|integer|min:1',
            'currency' => 'sometimes|bail|required|size:3|exists:currencies,name',
            'photo' => 'sometimes|required|image|max:4096',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('currency')) {
            $this->merge([
                'currency' => strtoupper($this->currency),
            ]);
        }
    }
}
