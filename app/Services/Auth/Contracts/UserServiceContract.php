<?php

namespace App\Services\Auth\Contracts;

use App\Models\User;

interface UserServiceContract
{

    public function create(array $data);

    public function getToken(array $data);

    public function createToken(User $user);

    public function breakToken();

    public function authenticate();

}