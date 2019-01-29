<?php
declare(strict_types = 1);

namespace App\Services\Favorite\Contracts;

use Exception;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;

interface FavoriteServiceContract
{
    /**
     * @param array $data
     * @return string
     * @throws Throwable
     * @throws JWTException
     * @throws Exception
     */
    public function action(array $data);

}
