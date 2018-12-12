<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Auth\Contracts\UserServiceContract;
use JWTAuth;

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
     * @return \Illuminate\Database\Eloquent\Model | bool
     * @throws \Throwable
     */
    public function create(array $data)
    {
        $user = $this->model->query()->make()->fill([
            'fname' => array_get($data, 'fname'),
            'lname' => array_get($data, 'lname'),
            'email' => array_get($data, 'email'),
            'password' => bcrypt(array_get($data, 'password')),
        ]);

        /*try {
            $user->save();
        } catch (AuthenticationException $e){

        }*/

        if ($user->saveOrFail()) {
            return $user;
        }
        return false;
    }


    /**
     * Create token for auth User
     *
     * @param array $data
     * @return string
     */
    public function getToken(array $data): string
    {
        $pwd = $data['current_password'] ? $data['current_password'] : $data['password'];
        return JWTAuth::attempt(['email' => $data['email'], 'password' => $pwd]);
    }


    /**
     * Create token for new User
     *
     * @param User $user
     * @return string
     */
    public function createToken(User $user)
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
     */
    public function authenticate()
    {
        return JWTAuth::parseToken()->authenticate();
    }


    /**
     * Reset user password
     *
     * @param array $data
     */
    public function resetPassword(array $data)
    {
        dd($data);
        try {
            $user = $this->model->query()
                ->where('email', '=', $data['email'])
                ->update([
                    'password' => bcrypt($data['password'])
                ]);
        } catch (\Throwable $e) {
            return abort(500, $e->getMessage());
        }
    }
}