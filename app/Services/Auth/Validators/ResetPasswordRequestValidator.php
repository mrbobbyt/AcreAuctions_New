<?php
declare(strict_types = 1);

namespace App\Services\Auth\Validators;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Validator;

class ResetPasswordRequestValidator implements AbstractValidator
{

    /**
     * Return validated array of data
     * @param Request $request
     * @throws ValidationException
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
     * @param Request $request
     * @throws ValidationException
     * @return array
     */
    public function validateBody(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:users,email',
            'password' => ['required', 'max:255', 'min:6', 'string', 'confirmed']
        ]);

        return $validator->validate();
    }
}
