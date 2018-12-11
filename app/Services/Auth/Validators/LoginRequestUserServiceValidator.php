<?php

namespace App\Services\Auth\Validators;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginRequestUserServiceValidator
{

    /**
     * Return validated array of data
     *
     * @param Request $request
     * @return array
     */
    public function attempt(Request $request): array
    {
        // Can include into returned data some another data like
        /*return [
            'company' => $company,
            'body' => $this->validateBody($request)
        ];*/

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
        $validator = Validator::make( $request->all(), [
                'email' => 'required|string|email|max:255|exists:users,email',
                'password'=> 'required'
            ], [
                'email.exists' => 'The email or the password is wrong.',
            ]
        );

        return $validator->validate();
    }
}