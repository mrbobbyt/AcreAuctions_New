<?php

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
     * @param  User  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Hash::check(request()->input('password'), $this->user->password);
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