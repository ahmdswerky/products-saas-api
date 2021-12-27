<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Quantity implements Rule
{
    protected $quantity;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->quantity >= 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Product is out of stock.';
    }
}
