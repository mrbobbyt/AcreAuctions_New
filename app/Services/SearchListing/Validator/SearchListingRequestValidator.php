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
            'address' => 'nullable|string', // county, city, zip

            'minSize' => 'nullable|numeric',
            'maxSize' => 'nullable|numeric',

            'minPrice' => 'nullable|numeric',
            'maxPrice' => 'nullable|numeric',

            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',

            'state' => 'nullable|string',
            'county' => 'nullable|string',

            'property_type' => 'nullable|string|exists:property_type,id',
            'sale_type' => 'nullable|string|exists:sale_type,id',
        ], [
            'property_type.exists' => 'Property type not found.',
            'sale_type.exists' => 'Financing type not found.',
        ]);

        return $validator->validate();
    }
}