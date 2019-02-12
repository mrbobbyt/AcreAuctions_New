<?php
declare(strict_types = 1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckExportType implements Rule
{
    /**
     * Determine if the validation rule passes.
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $types = ['csv', 'pdf', 'xls', 'html'];
        return in_array($value, $types);
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message()
    {
        return 'The invalid file type.';
    }
}
