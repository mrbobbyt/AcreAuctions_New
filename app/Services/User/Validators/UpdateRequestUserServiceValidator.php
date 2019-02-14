<?php
declare(strict_types = 1);

namespace App\Services\User\Validators;

use App\Rules\CheckRole;
use App\Services\Auth\Validators\AbstractValidator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Validator;

class UpdateRequestUserServiceValidator implements AbstractValidator
{
    /**
     * Return validated array of data
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    public function attempt(Request $request)
    {
        return [
            'body' => $this->validateBody($request),
            'image' => $this->validateImage($request),
            'tel' => $this->validateTelephone($request),
            'address' => $this->validateAddress($request),
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
            'fname' => 'nullable|string|max:255|min:3',
            'lname' => 'nullable|string|max:255|min:3',
            'email' => 'nullable|string|email|max:255|unique:users',
            'role' => ['nullable','integer', new CheckRole]
        ]);

        return $validator->validate();
    }


    /**
     * Validate avatar
     * @param Request $request
     * @throws ValidationException
     * @return array
     */
    protected function validateImage(Request $request)
    {
        $validator = Validator::make($request->only('avatar'), [
            'image' => 'nullable|image',
        ]);

        return $validator->validate();
    }


    /**
     * Validate telephone
     * @param Request $request
     * @throws ValidationException
     * @return array
     */
    protected function validateTelephone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tel' => 'array',
            'tel.phone' => 'nullable|numeric',
            'tel.fax' => 'nullable|numeric',
            'tel.toll_free' => 'nullable|numeric',
        ]);

        return $validator->validate();
    }


    /**
     * Validate address
     * @param Request $request
     * @throws ValidationException
     * @return array
     */
    protected function validateAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_first' => 'nullable|string',
            'address_second' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'zip' => 'nullable|numeric',
        ]);

        return $validator->validate();
    }
}
