<?php
declare(strict_types = 1);

namespace App\Repositories\Post\Exceptions;

use Exception;

class PostNotFoundException extends Exception
{
    protected $message = 'Post not found.';
}
