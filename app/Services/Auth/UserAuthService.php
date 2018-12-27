<?php
declare(strict_types = 1);

namespace App\Services\Auth;

use App\Mail\ForgotPasswordMail;
use App\Models\Image;
use App\Models\PasswordResets;
use App\Models\User;
use Exception;
use Mail;

use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\Auth\Contracts\UserAuthServiceContract;

use JWTAuth;
use Throwable;
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
     * @param array $data
     * @return object
     * @throws Throwable
     */
    public function create(array $data): object
    {
        $data['password'] = bcrypt(array_get($data, 'password'));
        $user = $this->model->query()->make()->fill($data);
        $user->saveOrFail();

        return $user;
    }


    /**
     * Create token for auth User
     * @param array $data
     * @return string
     * @throws Exception
     */
    public function getToken(array $data): string
    {
        if($token = JWTAuth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            return $token;
        }

        throw new Exception('The email or the password is wrong.');
    }


    /**
     * Create token for new User
     * @param $data
     * @return string
     * @throws JWTException
     */
    public function createToken($data): string
    {
        if (isset($data['password'])) {
            if ($token = JWTAuth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
                return $token;
            }
        } else {
            return $this->createTokenWithoutPassword($data['email']);
        }

        throw new JWTException();
    }


    /**
     * Logout user and break token
     */
    public function breakToken()
    {
        return JWTAuth::invalidate(JWTAuth::getToken());
    }


    /**
     * Reset user password
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
     * @param array $data
     * @throws Throwable
     */
    public function sendEmailWithToken(array $data): void
    {
        $token = $this->createTokenWithoutPassword($data['body']['email']);
        $this->createForgotToken($data, $token);

        Mail::to($data['body']['email'])
            ->send(new ForgotPasswordMail($token));
    }


    /**
     * Create reset token when user forgot password
     * @param array $data
     * @param string $token
     * @throws Throwable
     * @return bool
     */
    protected function createForgotToken(array $data, string $token): bool
    {
        $pwd = PasswordResets::query()->make()->fill([
            'email' => $data['body']['email'],
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $pwd->saveOrFail();
    }


    /**
     * Create token without password
     * @param string $email
     * @return string
     * @throws JWTException
     */
    protected function createTokenWithoutPassword (string $email): string
    {
        $user = $this->userRepo->findByEmail($email);
        if ($token = JWTAuth::fromUser($user)) {
            return $token;
        }

        throw new JWTException();
    }


    /**
     * Create User avatar
     * @param array $data
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    public function createAvatar(array $data, $id): bool
    {
        $image = Image::query()->make()->fill([
            'entity_id' => $id,
            'entity_type' => Image::TYPE_USER_AVATAR,
            'name' => upload_image($data['avatar'], class_basename($this->model), 'avatar'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $image->saveOrFail();
    }
}
