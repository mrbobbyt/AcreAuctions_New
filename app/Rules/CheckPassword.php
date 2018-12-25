<?php
declare(strict_types = 1);

namespace App\Rules;

use App\Models\User;
use Hash;
use Illuminate\Contracts\Validation\Rule;

class CheckPassword implements Rule
{

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
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
        return Hash::check($value, $this->user->password);
    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The email or the password is wrong.';
    }
}