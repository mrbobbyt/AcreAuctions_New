<?php
declare(strict_types = 1);

namespace App\Services\Auth;

use App\Mail\ForgotPasswordMail;
use App\Mail\RegisterMail;
use App\Models\PasswordResets;
use App\Models\User;
use App\Models\RegisterToken;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        if (isset($data['body']['password'])) {
            $data['body']['password'] = bcrypt($data['body']['password']);
        }
        $user = $this->model->query()->make()->fill($data['body']);
        $user->saveOrFail();

        return $user;
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
     * Reset user password
     * @param array $data
     * @return bool
     * @throws ModelNotFoundException
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
     * @param string $reason
     * @throws Throwable
     */
    public function sendEmailWithToken(array $data, string $reason): void
    {
        $token = $this->createTokenWithoutPassword($data['body']['email']);
        if ($reason === 'forgot') {
            $this->createForgotToken($data, $token);
            Mail::to($data['body']['email'])
                ->send(new ForgotPasswordMail($token));
        } elseif ($reason === 'register') {
            $this->createRegisterToken($data, $token);
            $clientUrl = $data['body']['clientUrl'];
            Mail::to($data['body']['email'])
                ->send(new RegisterMail($clientUrl, $token));
        }
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
        ]);

        return $pwd->saveOrFail();
    }

    /**
     * Create token when user register
     * @param array $data
     * @param string $token
     * @throws Throwable
     * @return bool
     */
    protected function createRegisterToken(array $data, string $token): bool
    {
        $reg = RegisterToken::query()->make()->fill([
            'email' => $data['body']['email'],
            'token' => $token,
        ]);

        return $reg->saveOrFail();
    }


    /**
     * Create token without password
     * @param string $email
     * @return string
     * @throws JWTException
     */
    protected function createTokenWithoutPassword(string $email): string
    {
        $user = $this->userRepo->findByEmail($email);

        if ($token = JWTAuth::fromUser($user)) {
            return $token;
        }

        throw new JWTException();
    }


    /**
     * Create or login user via socials
     * @param array $data
     * @return array
     * @throws JWTException
     * @throws Throwable
     */
    public function createOrLogin(array $data): array
    {
        if ($this->userRepo->checkUserExists($data['body']['email'])) {
            $user = $this->userRepo->findByEmail($data['body']['email']);
        } else {
            $this->create($data);
            return ['register' => true];
        }
        $token = $this->createToken($data['body']);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }


    /**
     * Confirm user after registration
     * @param array $data
     * @return Model
     * @throws Exception
     */
    public function confirmUser(array $data): Model
    {
        $registerToken = RegisterToken::query()->where('token', $data['token'])->first();
        if ($user = $this->userRepo->findByEmail($registerToken->email)) {
            $registerToken->delete();
        }
        $user->email_verified_at = date('Y-m-d H:i:s');
        $user->save();

        return $user;
    }
}
