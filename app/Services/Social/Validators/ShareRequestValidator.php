<?php
declare(strict_types = 1);

namespace App\Services\Social\Validators;

use App\Services\Auth\Validators\AbstractValidator;
use Illuminate\Http\Request;
use Validator;

class ShareRequestValidator implements AbstractValidator
{
    /**
     * Return validated array of data
     * @param Request $request
     * @return array
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
            'network_id' => 'required|numeric|exists:networks,id',
            'listing_id' => 'required|numeric|exists:listings,id',
        ], [
            'network_id.exists' => 'Network is not found.',
            'listing_id.exists' => 'Listing is not found.',
        ]);

        return $validator->validate();
    }
}
