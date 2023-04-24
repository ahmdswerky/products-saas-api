<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class IntegrationStoreRequest extends FormRequest
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
            'integration_name_id' => 'required|exists:integration_names,id',
            'key' => 'required|min:2',
            'secret' => 'nullable|min:2',
            'is_used' => 'required|boolean',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_used' => $this->boolean('is_used'),
        ]);
    }
}
