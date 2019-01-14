<?php
declare(strict_types = 1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckUtilities implements Rule
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
        $utilities = range(1, 6);
        return in_array($value, $utilities);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The invalid listing utilities.';
    }
}
