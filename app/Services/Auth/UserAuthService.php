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
use Mail;

use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\Auth\Contracts\UserAuthServiceContract;

use JWTAuth;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        $user = $this->model->query()->make()->fill($data);
        $user->saveOrFail();

        return $user;
    }


    /**
     * Create token for new User
     * @param string $email
     * @param string $password
     * @return string
     * @throws JWTException
     */
    public function createToken(string $email, string $password = null): string
    {
        if (!isset($password)) {
            return $this->createTokenWithoutPassword($email);
        }

        if ($token = JWTAuth::attempt(['email' => $email, 'password' => $password])) {
            return $token;
        }

        throw new JWTException();
    }


    /**
     * Reset user password
     * @param string $email
     * @param string $password
     * @return bool
     * @throws Throwable
     */
    public function resetPassword(string $email, string $password): bool
    {
        $user = $this->userRepo->findByEmail($email);
        $user->password = bcrypt($password);

        return $user->saveOrFail();
    }


    /**
     * Send email with invitation token when user forgot password
     * @param string $reason
     * @param string $email
     * @param string $clientUrl
     * @throws JWTException
     * @throws Throwable
     */
    public function sendEmailWithToken(string $reason, string $email, string $clientUrl = null): void
    {
        $token = $this->createTokenWithoutPassword($email);
        $mail = null;

        switch ($reason) {
            case PasswordResets::EMAIL_REASON:
                $this->createForgotToken($email, $token);
                $mail = new ForgotPasswordMail($token);
                break;

            case RegisterToken::EMAIL_REASON:
                if ($clientUrl === null) {
                    throw new BadRequestHttpException('Incorrect client URL');
                }

                $this->createRegisterToken($email, $token);
                $mail = new RegisterMail($clientUrl, $token);
                break;

            default:
                throw new Exception('Incorrect reason');
                break;
        }

        Mail::to($email)->send($mail);
    }


    /**
     * Create reset token when user forgot password
     * @param string $email
     * @param string $token
     * @return bool
     * @throws Throwable
     */
    protected function createForgotToken(string $email, string $token): bool
    {
        $pwd = PasswordResets::query()->make()->fill([
            'email' => $email,
            'token' => $token,
        ]);

        return $pwd->saveOrFail();
    }

    /**
     * Create token when user register
     * @param string $email
     * @param string $token
     * @return bool
     * @throws Throwable
     */
    public function createRegisterToken(string $email, string $token): bool
    {
        $reg = RegisterToken::query()->make()->fill([
            'email' => $email,
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
