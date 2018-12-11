<?php

namespace App\Services\Auth\Exceptions;

use Exception;

class UserNotFoundException extends Exception
{
    protected $message = 'Invalid given data.';
}