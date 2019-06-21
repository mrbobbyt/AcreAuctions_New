<?php
declare(strict_types = 1);

namespace App\Services\Payment\Contracts;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Exceptions\JWTException;

interface PaymentServiceContract
{
    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Update user
     * @param string $data
     * @param int $id
     * @return false|JWTSubject
     * @throws JWTException
     */
    public function update(string $data, int $id);
}
