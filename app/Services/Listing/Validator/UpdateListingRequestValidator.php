<?php
declare(strict_types = 1);

namespace App\Services\Listing\Validator;

use App\Rules\CheckUtilities;
use App\Rules\CheckZoning;
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
            'apn' => 'nullable|numeric',
            'subtitle' => 'nullable|string|max:255|min:3',
            'description' => 'nullable|string',
            'utilities' => ['nullable', 'numeric', new CheckUtilities()],
            'zoning' => ['nullable', 'numeric', new CheckZoning()],
            'zoning_desc' => 'nullable|string',
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
            'acreage' => 'nullable, string',
            'state' => 'nullable|string',
            'county' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'road_access' => 'nullable|string',
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
            'monthly_payment' => 'nullable|numeric',
            'processing_fee' => 'nullable|numeric',
            'financial_term' => 'nullable|numeric',
            'percentage_rate' => 'nullable|numeric',
            'yearly_dues' => 'nullable|numeric',
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
            'doc.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:2048',
        ]);

        return $validator->validate();
    }
}
