<?php

namespace App\Services\Seller\Validators;

use Exception;
use Illuminate\Http\Request;
use Validator;

class UpdateSellerRequestValidator
{

    /**
     * Return validated array of data
     *
     * @param Request $request
     * @param int $id
     * @return array
     * @throws Exception
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
            'title' => 'nullable|string|max:255|min:3',
            'description' => 'nullable|string',
            'logo' => 'nullable|image',
            'cover' => 'nullable|image',
            'email' => 'nullable|string|email|max:255',
            'address' => 'nullable|string|max:255'
        ]);

        return $validator->validate();
    }
}