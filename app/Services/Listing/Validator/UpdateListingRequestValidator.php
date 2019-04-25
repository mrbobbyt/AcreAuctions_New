<?php
declare(strict_types = 1);

namespace App\Services\Listing\Validator;

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
     * @throws ValidationException
     * @return array
     */
    public function validateBody(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'apn' => 'nullable|string',
            'title' => 'nullable|string|max:255|min:3',
            'description' => 'nullable|string',
            'utilities' => 'array',
            'utilities.*' => 'nullable|numeric|exists:utilities,id',
            'zoning' => 'nullable|numeric|exists:zonings,id',
            'zoning_desc' => 'nullable|string',
            'property_type' => 'nullable|numeric|exists:property_types,id',
            'status' => 'nullable|numeric',
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
            'acreage' => 'nullable|numeric',
            'state' => 'nullable|string',
            'county' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'zip' => 'nullable|numeric',
            'road_access' => 'nullable|numeric',
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
            'price' => 'nullable|numeric',
            'sale_type' => 'nullable|numeric|exists:sale_types,id',
            'monthly_payment' => 'nullable|numeric',
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
     */
    public function validateDoc(Request $request): array
    {
        $validator = Validator::make($request->only('doc'), [
            'doc' => 'array',
            'doc.*' => 'nullable|file|max:2048',
        ]);

        return $validator->validate();
    }


    /**
     * Validate given data
     * @param Request $request
     * @return array
     */
    public function validateUrl(Request $request): array
    {

        $validator = Validator::make($request->only('links', 'video'), [
            'links' => 'array',
            'links.*.name' => ['nullable', 'string', 'url'],
            'links.*.description' => 'nullable|string',
            'video' => 'array',
            'video.*.name' => ['nullable', 'string', 'url'],
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
