<?php

namespace App\Services\Auth\Validators;

use Illuminate\Http\Request;
use Validator;

class LoginRequestUserServiceValidator implements AbstractValidator
{

    /**
     * Return validated array of data
     *
     * @param Request $request
     * @return array
     */
    public function attempt(Request $request): array
    {
        return [
            'body' => $this->validateBody($request)
        ];
    }


    /**
     * Validate given data
     *
     * @param Request $request
     * @return array
     */
    public function validateBody(Request $request): array
    {
        $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|exists:users,email',
                'password'=> 'required'
            ], [
                'email.exists' => 'The email or the password is wrong.',
            ]
        );

        return $validator->validate();
    }
}
