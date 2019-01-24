<?php
declare(strict_types = 1);

namespace App\Services\Listing\Validator;

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
            'price' => $this->validatePrice($request),
            'image' => $this->validateImage($request),
            'doc' => $this->validateDoc($request),
            'url' => $this->validateUrl($request),
            'subdivision' => $this->validateSub($request),
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
            'apn' => 'nullable|numeric',
            'title' => 'required|string|max:255|min:3',
            'description' => 'nullable|string',
            'utilities' => 'nullable|numeric|exists:utilities,id',
            'zoning' => 'nullable|numeric|exists:zonings,id',
            'zoning_desc' => 'nullable|string',
            'property_type' => 'nullable|numeric|exists:property_types,id',
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
            'acreage' => 'required|numeric',
            'state' => 'required|string',
            'county' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
            'zip' => 'required|numeric',
            'road_access' => 'nullable|numeric',
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
            'image' => 'array',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        return $validator->validate();
    }


    /**
     * Validate given data
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    public function validatePrice(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'price' => 'required|numeric',
            'monthly_payment' => 'nullable|numeric',
            'processing_fee' => 'nullable|numeric',
            'financial_term' => 'nullable|numeric',
            'percentage_rate' => 'nullable|numeric',
            'taxes' => 'nullable|numeric',
        ]);

        return $validator->validate();
    }


    /**
     * Validate given data
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    public function validateDoc(Request $request): array
    {
        $validator = Validator::make($request->only('doc'), [
            'doc' => 'array',
            'doc.*.name' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'doc.*.desc' => 'nullable|string',
        ]);

        return $validator->validate();
    }


    /**
     * Validate given data
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    public function validateUrl(Request $request): array
    {
        $urlRegex = '/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/';

        $validator = Validator::make($request->only('link', 'video'), [
            'link' => 'array',
            'link.*.name' => ['nullable', 'string', 'regex:'. $urlRegex],
            'link.*.desc' => 'nullable|string',
            'video' => 'array',
            'video.*.name' => ['nullable', 'string', 'regex:'. $urlRegex],
            'video.*.desc' => 'nullable|string',
        ]);

        return $validator->validate();
    }


    /**
     * Validate given data
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    public function validateSub(Request $request): array
    {
        $validator = Validator::make($request->only('subdivision'), [
            'subdivision' => 'array',
            'subdivision.name' => 'nullable|string',
            'subdivision.yearly_dues' => 'nullable|numeric',
        ]);

        return $validator->validate();
    }
}
