<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckPasswordMatch implements Rule
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
        $old = request()->input('current_password');
        $new = request()->input('password');

        return strcmp($old, $new);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The current password and the new password does not match.';
    }
}
