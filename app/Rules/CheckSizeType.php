<?php
declare(strict_types = 1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckSizeType implements Rule
{

    /**
     * Determine if the validation rule passes.
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $size = ['L', 'B'];
        return in_array($value, $size);
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message()
    {
        return 'The invalid size listing.';
    }
}
