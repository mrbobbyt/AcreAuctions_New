<?php
declare(strict_types = 1);

namespace App\Services\Seller\Validators;

use App\Services\Auth\Validators\AbstractValidator;
use Illuminate\Http\Request;
use Validator;

class CreateSellerRequestValidator implements AbstractValidator
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
            'email' => $this->validateEmail($request),
            'tel' => $this->validateTelephone($request),
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
            'title' => 'required|string|max:255|min:3',
            'description' => 'nullable|string',
            'email' => 'nullable|string|email|max:255',
            'address' => 'nullable|string|max:255'
        ]);

        return $validator->validate();
    }


    /**
     * Validate images
     * @param Request $request
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
     * @return array
     */
    protected function validateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email',
        ]);

        return $validator->validate();
    }


    /**
     * Validate telephone
     * @param Request $request
     * @return array
     */
    protected function validateTelephone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tel' => 'nullable|numeric',
        ]);

        return $validator->validate();
    }
}
