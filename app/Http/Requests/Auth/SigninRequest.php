<?php

namespace App\Http\Requests\Auth;

use App\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class SigninRequest extends FormRequest
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
            'email' => [
                'bail',
                'required',
                'email',
                'exists:users,email',
                new Password($this->email, $this->password),
            ],
            'password' => [
                'required',
                'min:6',
            ],
        ];
    }

    public function messages()
    {
        return [
            'email.exists' => __('auth.failed'),
        ];
    }
}
