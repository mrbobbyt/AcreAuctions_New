<?php
declare(strict_types = 1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckZoning implements Rule
{
    /**
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $zoning = range(1, 7);
        return in_array($value, $zoning);
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message()
    {
        return 'The invalid listing zoning.';
    }
}
