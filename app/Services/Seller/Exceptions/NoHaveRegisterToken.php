<?php
declare(strict_types = 1);

namespace App\Services\Seller\Exceptions;

use Exception;

class NoHaveRegisterToken extends Exception
{
    protected $message = 'Seller has been already added';
}