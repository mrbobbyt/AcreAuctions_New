<?php
declare(strict_types = 1);

namespace App\Repositories\User\Contracts;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Exceptions\JWTException;

interface UserRepositoryContract
{

    /**
     * Find user using id
     * @param int $id
     * @return Model
     */
    public function findByPk(int $id);


    /**
     * Find user using email
     * @param string $email
     * @return Model
     */
    public function findByEmail(string $email);


    /**
     * Check if user exists in db
     * @param string $email
     * @return bool
     */
    public function checkUserExists(string $email);


    /**
     * Return authenticate user
     * @throws JWTException
     * @return JWTSubject
     */
    public function authenticate();


    /**
     * Return authenticate user
     * @throws JWTException
     * @return int
     */
    public function getId();


    /**
     * Check user`s permission to make action
     * @param int $id
     * @return bool
     * @throws Exception
     * @throws JWTException
     */
    public function checkPermission(int $id);


    /**
     * @return bool
     */
    public function isAdmin();


    /**
     * Check existing token
     * @return bool
     */
    public function checkToken();

}
