<?php
declare(strict_types = 1);

namespace App\Repositories\User\Exceptions;

use Exception;

class NotEndedRegistrationException extends Exception
{
    protected $message = 'Registration is not completed. Please confirm your email.';
}