<?php
declare(strict_types = 1);

namespace App\Services\Favorite\Validator;

use App\Services\Auth\Validators\AbstractValidator;
use Illuminate\Http\Request;
use Validator;

class AddFavoriteValidateRequest implements AbstractValidator
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
            'listing_id' => 'required|numeric|exists:listings,id'
        ], [
            'listing_id.exists' => 'Listing not found.'
        ]);

        return $validator->validate();
    }
}