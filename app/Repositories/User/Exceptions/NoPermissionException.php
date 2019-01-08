<?php
declare(strict_types = 1);

namespace App\Repositories\User\Exceptions;

use Exception;

class NoPermissionException extends Exception
{
    protected $message = 'You have no permission.';
}