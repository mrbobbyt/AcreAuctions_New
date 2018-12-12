<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Auth\Contracts\UserServiceContract;
use Illuminate\Auth\AuthenticationException;
use JWTAuth;
use Throwable;

class UserService implements UserServiceContract

{

    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * Create User
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Throwable
     */
    public function create(array $data): \Illuminate\Database\Eloquent\Model
    {
        $data['password'] = bcrypt(array_get($data, 'password'));
        $user = $this->model->query()->make()->fill($data);

        try {
            $user->saveOrFail();
        } catch (AuthenticationException $e){
            return abort(401, $e->getMessage());
        }

        return $user;
    }


    /**
     * Create token for auth User
     *
     * @param array $data
     * @return string
     */
    public function getToken(array $data): string
    {
        return JWTAuth::attempt(['email' => $data['email'], 'password' => $data['password']]);
    }


    /**
     * Create token for auth User
     *
     * @param array $data
     * @return string
     */
    public function getResetToken(array $data): string
    {
        return JWTAuth::attempt(['email' => $data['email'], 'password' => $data['current_password']]);
    }

    /**
     * Create token for new User
     *
     * @param User $user
     * @return string
     */
    public function createToken(User $user): string
    {
        return JWTAuth::fromUser($user);
    }


    /**
     * Logout user and break token
     *
     */
    public function breakToken()
    {
        return JWTAuth::invalidate(JWTAuth::getToken());
    }


    /**
     * Return authenticate user
     *
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function authenticate()
    {
        return JWTAuth::parseToken()->authenticate();
    }


    /**
     * Reset user password
     *
     * @param array $data
     * @return bool
     */
    public function resetPassword(array $data): bool
    {
        try {
            $user = $this->model->query()
                ->where('email', '=', $data['email'])
                ->update([
                    'password' => bcrypt($data['password'])
                ]);
        } catch (Throwable $e) {
            return abort(500, $e->getMessage());
        }

        return $user;
    }
}