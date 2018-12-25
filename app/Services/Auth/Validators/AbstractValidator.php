<?php
declare(strict_types = 1);

namespace App\Services\Auth\Validators;

use Illuminate\Http\Request;
use Validator;

interface AbstractValidator
{

    /**
     * Return validated array of data
     *
     * Can include into returned data some another data like
     * return [
     *     'company' => $company,
     *     'body' => $this->validateBody($request)
     * ];
     *
     * @param Request $request
     * @return array
     */
    public function attempt(Request $request);


    /**
     * Validate given data
     *
     * @param Request $request
     * @return array
     */
    public function validateBody(Request $request);

}