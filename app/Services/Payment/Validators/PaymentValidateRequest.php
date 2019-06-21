<?php
declare(strict_types = 1);

namespace App\Services\Payment\Validators;

use App\Services\Auth\Validators\AbstractValidator;
use Illuminate\Http\Request;
use Validator;

class PaymentValidateRequest implements AbstractValidator
{
    /**
     * @param Request $request
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function attempt(Request $request)
    {
        return $this->validateBody($request);
    }


    /**
     * @param Request $request
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateBody(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'listing' => 'required|string',
            'payment_method' => 'required|string',
            'taxes' => 'required|numeric',
            'total' => 'required|numeric',
            'user' => 'required|numeric',
        ]);

        return $validator->validate();
    }
}
