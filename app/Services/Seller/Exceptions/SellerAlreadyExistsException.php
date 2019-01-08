<?php
declare(strict_types = 1);

namespace App\Services\Seller\Exceptions;

use Exception;

class SellerAlreadyExistsException extends Exception
{
    protected $message = 'Seller with the same title already exists, please, choose another.';
}