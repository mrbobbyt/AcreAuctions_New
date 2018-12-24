<?php

namespace App\Services\Seller\Validators;

use App\Services\Auth\Validators\AbstractValidator;
use Illuminate\Http\Request;
use Validator;

class CreateSellerRequestValidator implements AbstractValidator
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
            'title' => 'required|string|max:255|min:3',
            'description' => 'nullable|string',
            'logo' => 'nullable|image',
            'cover' => 'nullable|image',
            'email' => 'nullable|string|email|max:255',
            'address' => 'nullable|string|max:255'
        ]);

        return $validator->validate();
    }
}