<?php
declare(strict_types=1);

namespace App\Services\Seller\Contracts;

use App\Services\Seller\Exceptions\SellerAlreadyExistsException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Throwable;

interface SellerServiceContract
{
    /**
     * @param array $data
     * @return array
     * @throws Throwable
     */
    public function create(array $data);


    /**
     * Update seller
     * @param int $id
     * @param array $data
     * @return Model
     * @throws SellerAlreadyExistsException
     * @throws Throwable
     */
    public function update(array $data, int $id);


    /**
     * Delete seller
     * @param int $id
     * @throws Exception
     * @return bool
     */
    public function delete(int $id);

    /**
     * Send mail for seller
     * @param string $clientUrl
     * @param string $email
     * @param string $token
     * @return mixed
     */
    public function sendAuthMail(string $clientUrl, string $email, string $token);


    /**
     * @param array $data
     * @return mixed
     */
    public function authSeller(array $data);
}
