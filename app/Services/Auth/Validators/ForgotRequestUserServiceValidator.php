<?php
declare(strict_types = 1);

namespace App\Services\Auth\Validators;

use Illuminate\Http\Request;
use Validator;

class ForgotRequestUserServiceValidator implements AbstractValidator
{
    /**
     * Return validated array of data
     * @param Request $request
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function attempt(Request $request)
    {
        return $this->validateBody($request);
    }

    /**
     * Validate given data
     * @param Request $request
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateBody(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'clientUrl' => 'required|string|max:255|min:5'
        ]);

        return $validator->validate();
    }
}
