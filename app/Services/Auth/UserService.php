<?php

namespace App\Services\Auth;

use App\Mail\ForgotPasswordMail;
use App\Models\User;
use App\Models\PasswordResets;
use App\Services\Auth\Contracts\UserServiceContract;
use Illuminate\Auth\AuthenticationException;
use JWTAuth;
use Mail;
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


    /**
     * Send email with invitation token when user forgot password
     *
     * @param array $data
     */
    public function sendEmailWithToken(array $data)
    {
        try {
            // save token
            $this->createForgotToken($data);

            Mail::to($data['body']['email'])
                ->send(new ForgotPasswordMail($data['token']));

        } catch (Throwable $e) {
            return abort(500, $e->getMessage());
        }

    }


    /**
     * Create reset token when user forgot password
     *
     * @param array $data
     * @throws Throwable
     */
    public function createForgotToken(array $data)
    {
        $pwd = PasswordResets::query()->make()->fill([
            'email' => $data['body']['email'],
            'token' => $data['token'],
            'created_at' => date('Y-m-d H:i:s')
        ]);

        try {
            $pwd->saveOrFail();
        } catch (AuthenticationException $e){
            return abort(401, $e->getMessage());
        }
    }
}