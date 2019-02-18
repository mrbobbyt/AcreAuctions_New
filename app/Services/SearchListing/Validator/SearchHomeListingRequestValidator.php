<?php
declare(strict_types = 1);

namespace App\Services\SearchListing\Validator;

use App\Services\Auth\Validators\AbstractValidator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Validator;

class SearchHomeListingRequestValidator implements AbstractValidator
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
     */
    public function validateBody(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'nullable|string', // county, city, zip

            'state' => 'nullable|string',
            'acreage' => 'nullable|numeric',
            'price' => 'nullable|numeric',

            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
        ]);

        return $validator->validate();
    }
}
