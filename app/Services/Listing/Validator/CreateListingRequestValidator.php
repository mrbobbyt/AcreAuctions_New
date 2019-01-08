<?php
declare(strict_types = 1);

namespace App\Services\Listing\Validator;

use App\Rules\CheckSizeType;
use App\Services\Auth\Validators\AbstractValidator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Validator;

class CreateListingRequestValidator implements AbstractValidator
{
    /**
     * Return validated array of data
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    public function attempt(Request $request): array
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
     * @return array
     * @throws ValidationException
     */
    public function validateBody(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|min:3',
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
            'size_type' => ['required', 'string', new CheckSizeType],
            'state' => 'required|string',
            'county' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
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
