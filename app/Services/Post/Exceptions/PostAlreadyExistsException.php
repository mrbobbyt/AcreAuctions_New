<?php
declare(strict_types = 1);

namespace App\Services\Post\Exceptions;

use Exception;

class PostAlreadyExistsException extends Exception
{
    protected $message = 'Post with the same title already exists, please, choose another.';
}
