<?php
declare(strict_types = 1);

namespace App\Services\Seller\Validators;

use Exception;
use Illuminate\Http\Request;
use Validator;

class UpdateSellerRequestValidator
{

    /**
     * Return validated array of data
     * @param Request $request
     * @param int $id
     * @return array
     * @throws Exception
     */
    public function attempt(Request $request)
    {
        return [
            'body' => $this->validateBody($request),
            'image' => $this->validateImage($request)
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
            'title' => 'nullable|string|max:255|min:3',
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
    public function validateImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'nullable|image',
            'cover' => 'nullable|image',
        ]);

        return $validator->validate();
    }

}
