<?php
declare(strict_types = 1);

namespace App\Services\Admin\Validators;

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
            'body' => $this->validateBody($request)
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
            'county' => 'nullable|string',
            'status' => 'nullable|numeric|exists:listing_statuses,id|',
        ]);

        return $validator->validate();
    }
}
