<?php
declare(strict_types = 1);

namespace App\Services\Geocoding\Validators;

use App\Services\Auth\Validators\AbstractValidator;
use Illuminate\Http\Request;
use Validator;

class ReverseGeocodingValidateRequest implements AbstractValidator
{
    /**
     * Return validated array of data
     * @param Request $request
     * @return array
     */
    public function attempt(Request $request)
    {
        return $this->validateBody($request);
    }


    /**
     * Validate given data
     * @param Request $request
     * @return array
     */
    public function validateBody(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        return $validator->validate();
    }
}
