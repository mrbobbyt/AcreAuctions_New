<?php

namespace App\Services\Auth;

use App\Mail\ForgotPasswordMail;
use App\Models\User;
use App\Models\PasswordResets;
use Mail;

use App\Repositories\Auth\Contracts\UserRepoContract;
use App\Services\Auth\Contracts\UserServiceContract;

use JWTAuth;
use Throwable;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserService implements UserServiceContract

{

    protected $model;
    protected $userRepo;


    public function __construct(User $user, UserRepoContract $userRepo)
    {
        $this->model = $user;
        $this->userRepo = $userRepo;
    }


    /**
     * Create User
     *
     * @param array $data
     * @return Model
     * @throws Throwable
     */
    public function create(array $data): Model
    {
        $data['password'] = bcrypt(array_get($data, 'password'));
        $user = $this->model->query()->make()->fill($data);
        $user->saveOrFail();

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
     * @param $user
     * @return string
     */
    public function createToken($user): string
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
     * @throws JWTException
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
     * @throws Throwable
     */
    public function resetPassword(array $data): bool
    {
        $user = $this->userRepo->findByEmail($data['email']);
        $user->password = bcrypt($data['password']);

        return $user->saveOrFail();
    }


    /**
     * Send email with invitation token when user forgot password
     *
     * @param array $data
     * @throws Throwable
     */
    public function sendEmailWithToken(array $data): void
    {
        $this->createForgotToken($data);

        Mail::to($data['body']['email'])
            ->send(new ForgotPasswordMail($data['token']));
    }


    /**
     * Create reset token when user forgot password
     *
     * @param array $data
     * @throws Throwable
     * @return bool
     */
    public function createForgotToken(array $data): bool
    {
        $pwd = PasswordResets::query()->make()->fill([
            'email' => $data['body']['email'],
            'token' => $data['token'],
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $pwd->saveOrFail();
    }
}