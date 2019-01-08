<?php
declare(strict_types = 1);

namespace App\Repositories\Seller\Exceptions;

use Exception;

class SellerNotVerifiedException extends Exception
{
    protected $message = 'Seller is not verified.';
}