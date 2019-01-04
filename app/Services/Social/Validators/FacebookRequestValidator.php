<?php
declare(strict_types = 1);

namespace App\Services\Social\Validators;

use Facebook\GraphNodes\GraphNode;
use Validator;

class FacebookRequestValidator
{

    /**
     * Return validated array of data
     * @param GraphNode $json
     * @return array
     */
    public function attempt(GraphNode $json)
    {
        $data = json_decode($json->asJson(), true);
        $name = explode(' ', $data['name']);
        $user = [
            'email' => $data['email'],
            'fname' => $name[0],
            'lname' => $name[1]
        ];

        return [
            'body' => $this->validateBody($user)
        ];
    }

    /**
     * Validate given data
     * @param $user
     * @return array
     */
    public function validateBody($user)
    {
        $validator = Validator::make($user, [
            'email' => 'required|string|email|max:255',
            'fname' => 'required|string|max:255|min:3',
            'lname' => 'required|string|max:255|min:3',
        ]);

        return $validator->validate();
    }
}
