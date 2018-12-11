<?php

namespace App\Services\Auth\Validators;


class LoginRequestUserServiceValidator
{
    public $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }


    public function attempt() {


        $company = null;

        return [
            'company' => $company,
            'body' => $this->validateBody()
        ];
    }

    public function validateBody():array {
     // validator
    }
}