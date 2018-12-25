<?php
declare(strict_types = 1);

namespace App\Services\Auth\Validators;

use Illuminate\Http\Request;
use Validator;

class ForgotRequestUserServiceValidator implements AbstractValidator

{

    /**
     * Return validated array of data
     *
     * @param Request $request
     * @return array
     */
    public function attempt(Request $request)
    {
        $token = bcrypt(str_random(10));

        return [
            'token' => $token,
            'body' => $this->validateBody($request)
        ];
    }

    /**
     * Validate given data
     *
     * @param Request $request
     * @return array
     */
    public function validateBody(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:users,email',
        ], [
            'email.exists' => 'User not found.'
        ]);

        return $validator->validate();
    }
}