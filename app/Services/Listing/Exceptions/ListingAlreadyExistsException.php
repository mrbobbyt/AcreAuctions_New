<?php
declare(strict_types = 1);

namespace App\Services\Listing\Exceptions;

use Exception;

class ListingAlreadyExistsException extends Exception
{
    protected $message = 'Listing with the same title already exists, please, choose another.';
}
