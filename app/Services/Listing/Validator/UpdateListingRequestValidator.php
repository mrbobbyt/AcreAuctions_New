<?php
declare(strict_types = 1);

namespace App\Services\Listing\Validator;

use App\Rules\CheckSizeType;
use App\Services\Auth\Validators\AbstractValidator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Validator;

class UpdateListingRequestValidator implements AbstractValidator
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
            'geo' => $this->validateGeo($request),
            'image' => $this->validateImage($request),
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
            'subtitle' => 'nullable|string|max:255|min:3',
            'description' => 'nullable|string',
        ]);

        return $validator->validate();
    }


    /**
     * Validate given data
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    public function validateGeo(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'size_type' => ['nullable', 'string', new CheckSizeType],
            'state' => 'nullable|string',
            'county' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
        ]);

        return $validator->validate();
    }


    /**
     * Validate given data
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    public function validateImage(Request $request): array
    {
        $validator = Validator::make($request->only('image'), [
            'image' => 'nullable|image',
        ]);

        return $validator->validate();
    }

}
