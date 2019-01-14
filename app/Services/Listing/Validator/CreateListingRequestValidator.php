<?php
declare(strict_types = 1);

namespace App\Services\Listing\Validator;

use App\Rules\CheckUtilities;
use App\Rules\CheckZoning;
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
            'apn' => 'required|numeric',
            'subtitle' => 'nullable|string|max:255|min:3',
            'description' => 'nullable|string',
            'utilities' => ['required', 'numeric', new CheckUtilities()],
            'zoning' => ['required', 'numeric', new CheckZoning()],
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
            'acreage' => 'required|numeric',
            'state' => 'required|string',
            'county' => 'nullable|string',
            'city' => 'required|string',
            'address' => 'required|string',
            'road_access' => 'nullable|string',
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
            'financial_term' => 'required|numeric',
            'percentage_rate' => 'required|numeric',
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
            'doc' => 'nullable|file',
        ]);

        return $validator->validate();
    }
}
