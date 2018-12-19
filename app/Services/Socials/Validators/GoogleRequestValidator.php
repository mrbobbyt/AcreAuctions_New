<?php

namespace App\Services\Socials\Validators;

use Google_Service_Oauth2_Userinfoplus;
use Validator;

class GoogleRequestValidator
{

    /**
     * Return validated array of data
     *
     * @param Google_Service_Oauth2_Userinfoplus $data
     * @return array
     */
    public function attempt(Google_Service_Oauth2_Userinfoplus $data)
    {
        $user = [
            'email' => $data->getEmail(),
            'fname' => $data->getGivenName(),
            'lname' => $data->getFamilyName(),
        ];

        return [
            'body' => $this->validateBody($user)
        ];
    }

    /**
     * Validate given data
     *
     * @param $user
     * @return array
     */
    public function validateBody($user)
    {
        $validator = Validator::make($user, [
            'email' => 'required|string|email|max:255|unique:users',
            'fname' => 'required|string|max:255|min:3',
            'lname' => 'required|string|max:255|min:3',
        ]);

        return $validator->validate();
    }

}
