<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Auth\Contracts\UserServiceContract;
use Illuminate\Auth\AuthenticationException;

class UserService implements UserServiceContract
{

    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model | bool
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

        if ($user->save()) {
            return $user;
        }
        return false;
    }
}