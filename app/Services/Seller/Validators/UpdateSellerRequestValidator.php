<?php
declare(strict_types = 1);

namespace App\Services\Seller\Validators;

use App\Services\Auth\Validators\AbstractValidator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Validator;

class UpdateSellerRequestValidator implements AbstractValidator
{
    /**
     * Return validated array of data
     * @param Request $request
     * @param int $id
     * @return array
     * @throws ValidationException
     */
    public function attempt(Request $request)
    {
        return [
            'body' => $this->validateBody($request),
            'image' => $this->validateImage($request),
            'email' => $this->validateEmail($request),
            'tel' => $this->validateTelephone($request),
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
            'title' => 'nullable|string|max:255|min:3',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255'
        ]);

        return $validator->validate();
    }


    /**
     * Validate images
     * @param Request $request
     * @throws ValidationException
     * @return array
     */
    protected function validateImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'nullable|image',
            'cover' => 'nullable|image',
        ]);

        return $validator->validate();
    }


    /**
     * Validate email
     * @param Request $request
     * @throws ValidationException
     * @return array
     */
    protected function validateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'array',
            'email.*' => 'nullable|email',
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
            'tel.*' => 'nullable|numeric',
        ]);

        return $validator->validate();
    }
}
