<?php
declare(strict_types = 1);

namespace App\Rules;

use Hash;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class CheckPassword implements Rule
{
    protected $user;

    public function __construct(Model $user)
    {
        $this->user = $user;
    }


    /**
     * Determine if the validation rule passes.
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Hash::check($value, $this->user->password);
    }


    /**
     * Get the validation error message.
     * @return string
     */
    public function message()
    {
        return 'Incorrect username or password.';
    }
}
