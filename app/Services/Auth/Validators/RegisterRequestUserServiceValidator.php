<?php
declare(strict_types = 1);

namespace App\Services\Auth\Validators;

use App\Rules\CheckRole;
use Illuminate\Http\Request;
use Validator;

class RegisterRequestUserServiceValidator implements AbstractValidator
{

    /**
     * Return validated array of data
     * @param Request $request
     * @return array
     */
    public function attempt(Request $request)
    {
        return [
            'body' => $this->validateBody($request),
            'image' => $this->validateImage($request),
        ];
    }


    /**
     * Validate given data
     * @param Request $request
     * @return array
     */
    public function validateBody(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'fname' => 'required|string|max:255|min:3',
            'lname' => 'required|string|max:255|min:3',
            'password'=> 'required|max:255|min:6|confirmed|string',
            'role' => ['integer', 'nullable', new CheckRole],
        ]);

        return $validator->validate();
    }


    /**
     * Validate avatar
     * @param Request $request
     * @return array
     */
    protected function validateImage(Request $request)
    {
        $validator = Validator::make($request->only('avatar'), [
            'avatar' => 'nullable|image'
        ]);

        return $validator->validate();
    }
}
