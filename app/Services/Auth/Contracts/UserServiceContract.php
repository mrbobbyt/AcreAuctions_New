<?php

namespace App\Services\Auth\Contracts;


interface UserServiceContract
{

    public function create(array $data);

    public function getToken(array $data);
}