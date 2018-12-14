<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckRole implements Rule
{

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $roles = [2, 3];
        return in_array($value, $roles);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The invalid user role.';
    }
}
