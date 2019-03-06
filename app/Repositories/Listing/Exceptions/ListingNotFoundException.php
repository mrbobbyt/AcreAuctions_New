<?php
declare(strict_types = 1);

namespace App\Repositories\Listing\Exceptions;

use Exception;

class ListingNotFoundException extends Exception
{
    protected $message = 'Listing not found.';
}
