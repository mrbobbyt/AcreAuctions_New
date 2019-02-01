<?php
declare(strict_types = 1);

namespace App\Services\SearchListing\Validator;

use App\Services\Auth\Validators\AbstractValidator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Validator;

class SearchListingRequestValidator implements AbstractValidator
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
        ];
    }

    /**
     * Validate given data
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    public function validateBody(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'state' => 'nullable|string',
            'acreage' => 'nullable|numeric',
            'price' => 'nullable|numeric',

            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',

            'county' => 'nullable|string',
            'city' => 'nullable|string',
            'zip' => 'nullable|numeric',

            'property_type' => 'nullable|numeric|exists:property_types,id',
            'sale_type' => 'nullable|numeric|exists:sale_types,id',
        ], [
            'property_type.exists' => 'Property type not found.',
            'sale_type.exists' => 'Financing type not found.',
        ]);

        return $validator->validate();
    }
}