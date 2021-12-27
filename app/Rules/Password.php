<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class Password implements Rule
{
    protected $email;

    protected $password;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($email, $password)
    {
        $this->email = $email;

        $this->password = $password;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  string  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $user = User::byEmail($this->email)
            ->select('password')
            ->first();

        if (!$user) {
            return false;
        }

        return Hash::check($this->password, $user->password);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('auth.failed');
    }
}
