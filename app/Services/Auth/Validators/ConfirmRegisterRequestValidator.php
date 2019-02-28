<?php
declare(strict_types = 1);

namespace App\Services\Auth\Validators;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Validator;

class ConfirmRegisterRequestValidator implements AbstractValidator
{

    /**
     * Return validated array of data
     * @param Request $request
     * @throws ValidationException
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
     * @param Request $request
     * @throws ValidationException
     * @return array
     */
    public function validateBody(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|exists:register_tokens,token',
        ], [
            'token.exists' => 'User not found.'
        ]);

        return $validator->validate();
    }
}