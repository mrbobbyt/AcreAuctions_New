<?php

namespace App\Services\User\Validators;

use App\Rules\CheckRole;
use App\Services\Auth\Validators\AbstractValidator;
use Illuminate\Http\Request;
use Validator;

class UpdateRequestUserServiceValidator implements AbstractValidator
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
            'fname' => 'nullable|string|max:255|min:3',
            'lname' => 'nullable|string|max:255|min:3',
            'email' => 'nullable|string|email|max:255|unique:users',
            'role' => ['nullable','integer', new CheckRole]
        ]);

        return $validator->validate();
    }
}