<?php

namespace App\Services\Auth\Validators;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterRequestUserServiceValidator implements AbstractValidator
{

    /**
     * Return validated array of data
     *
     * @param Request $request
     * @return array
     */
    public function attempt(Request $request)
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
    public function validateBody(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'fname' => 'string|max:255|min:3',
            'lname' => 'string|max:255|min:3',
            'password'=> 'required|max:255|min:6|confirmed|string',
        ]);

        return $validator->validate();
    }
}