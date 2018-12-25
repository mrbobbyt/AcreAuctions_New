<?php
declare(strict_types = 1);

namespace App\Services\Auth;

use App\Mail\ForgotPasswordMail;
use App\Models\PasswordResets;
use App\Models\User;
use Mail;

use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\Auth\Contracts\UserAuthServiceContract;

use JWTAuth;
use Throwable;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserAuthService implements UserAuthServiceContract

{

    protected $model;
    protected $userRepo;


    public function __construct(User $user, UserRepositoryContract $userRepo)
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

        return $user->saveOrFail();
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
     * @throws JWTException
     */
    public function createToken($user): string
    {
        if ($token = JWTAuth::fromUser($user)) {
            return $token;
        }

        throw new JWTException();
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
