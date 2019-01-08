<?php
declare(strict_types = 1);

namespace App\Services\Auth\Validators;

use Illuminate\Http\Request;

interface AbstractValidator
{
    /**
     * Return validated array of data
     * @param Request $request
     * @return array
     */
    public function attempt(Request $request);


    /**
     * Validate given data
     * @param Request $request
     * @return array
     */
    public function validateBody(Request $request);

}