<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\PasswordResets;
use App\Models\RegisterToken;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Repositories\User\Exceptions\NotEndedRegistrationException;
use App\Services\Auth\Contracts\UserAuthServiceContract;
use App\Services\Auth\Validators\ConfirmRegisterRequestValidator;
use App\Services\Social\Contracts\FacebookServiceContract;
use App\Services\Social\Contracts\GoogleServiceContract;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

use App\Services\Auth\Validators\ForgotRequestUserServiceValidator;
use App\Services\Auth\Validators\RegisterRequestUserServiceValidator;
use App\Services\Auth\Validators\ResetPasswordRequestValidator;
use App\Services\Auth\Validators\LoginRequestUserServiceValidator;
use App\Services\Social\Validators\FacebookRequestValidator;
use App\Services\Social\Validators\GoogleRequestValidator;

use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\ValidationException;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthController extends Controller
{
    protected $userService;
    protected $userRepo;
    protected $fbService;
    protected $googleService;

    public function __construct(
        UserAuthServiceContract $userService,
        UserRepositoryContract $userRepo,
        FacebookServiceContract $fbService,
        GoogleServiceContract $googleService
    )
    {
        $this->userService = $userService;
        $this->userRepo = $userRepo;
        $this->fbService = $fbService;
        $this->googleService = $googleService;
    }


    /**
     * Login page for social networks
     * METHOD: get
     * URL: /
     * @return Response
     */
    public function index(): Response
    {
        // Get url to socials login
        try {
            $loginUrlFb = $this->fbService->getLogin();
            $loginUrlGoogle = $this->googleService->getLogin();
        } catch (FacebookSDKException $e) {
            // When Graph returns an error
            return \response(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);

        } catch (Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_I_AM_A_TEAPOT);
        }

        return \response(['loginUrlFb' => $loginUrlFb, 'loginUrlGoogle' => $loginUrlGoogle]);
    }


    /**
     * @return Response
     */
    public function handleSocials()
    {
        // Handle request from socials
        if (request('code') && request('scope')) {
            return $this->handleGoogle();
        } else if (request('code') && request('state')) {
            return $this->handleFacebook();
        }
    }


    /**
     * METHOD: post
     * URL: /login
     * @param Request $request
     * @return Response
     */
    public function login(Request $request): Response
    {
        try {
            $data = app(LoginRequestUserServiceValidator::class)->attempt($request);
            $this->userRepo->checkCompleteRegister($data['user']);
            $token = $this->userService->createToken(
                $data['body']['email'],
                $data['body']['password']
            );

        } catch (ValidationException $e) {
            return \response(['message' => $e->validator->errors()->first()], Response::HTTP_BAD_REQUEST);
        } catch (ModelNotFoundException $e) {
            return \response(['message' => 'User not exist.'], Response::HTTP_NOT_FOUND);
        } catch (NotEndedRegistrationException $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        } catch (JWTException | Throwable $e) {
            return \response(['message' => 'Can not login.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['token' => $token, 'user' => UserResource::make($data['user'])]);
    }


    /**
     * METHOD: post
     * URL: /register
     * @param Request $request
     * @return Response
     */
    public function register(Request $request): Response
    {
        try {
            $data = (new RegisterRequestUserServiceValidator())->attempt($request);
            $this->userService->create($data);

            return $this->sendEmail(
                RegisterToken::EMAIL_REASON,
                $data['email'],
                $data['clientUrl']
            );

        } catch (ValidationException $e) {
            return \response(['message' => $e->validator->errors()->first()], Response::HTTP_BAD_REQUEST);

        } catch (Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_I_AM_A_TEAPOT);
        }
    }


    /**
     * Get validated user data from Google
     * @return Response
     */
    protected function handleFacebook(): Response
    {
        try {
            $client = $this->fbService->getProfile();
            $data = (new FacebookRequestValidator())->attempt($client);
            $user = $this->userService->createOrLogin($data);

            if (isset($user['register'])) {
                return $this->sendEmail(RegisterToken::EMAIL_REASON, $data['email']);
            }

        } catch (FacebookResponseException | FacebookSDKException $e) {
            // When Graph returns an error
            return \response(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);

        } catch (ValidationException $e) {
            return \response(['message' => $e->validator->errors()->first()], Response::HTTP_BAD_REQUEST);

        } catch (JWTException | Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_I_AM_A_TEAPOT);
        }

        return \response(['token' => $user['token'], 'user' => UserResource::make($user['user'])]);
    }


    /**
     * Get validated user data from Google
     * @return Response
     */
    protected function handleGoogle(): Response
    {
        try {
            $client = $this->googleService->getProfile();
            $data = (new GoogleRequestValidator())->attempt($client);
            $user = $this->userService->createOrLogin($data);

            if (isset($user['register'])) {
                return $this->sendEmail(RegisterToken::EMAIL_REASON, $data['email']);
            }

        } catch (ValidationException $e) {
            return \response(['message' => $e->validator->errors()->first()], Response::HTTP_BAD_REQUEST);

        } catch (JWTException | Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_I_AM_A_TEAPOT);
        }

        return \response(['token' => $user['token'], 'user' => UserResource::make($user['user'])]);
    }


    /**
     * METHOD: get
     * URL: /logout
     * @return Response
     */
    public function logout(): Response
    {
        try {
            $this->userRepo->breakToken();

        } catch (JWTException | Throwable $e) {
            return \response(['message' => 'User logout error.', Response::HTTP_INTERNAL_SERVER_ERROR]);
        }

        return \response(['User logged out successfully.', Response::HTTP_OK]);
    }


    /**
     * METHOD: post
     * URL: /forgot
     * @param Request $request
     * @return Response
     */
    public function forgotPassword(Request $request): Response
    {
        try {
            $data = (new ForgotRequestUserServiceValidator())->attempt($request);
            return $this->sendEmail(PasswordResets::EMAIL_REASON, $data['email']);

        } catch (ValidationException $e) {
            return \response(['message' => $e->validator->errors()->first()], Response::HTTP_BAD_REQUEST);

        } catch (Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_I_AM_A_TEAPOT);
        }

        /** when user went on link with received token - redirect him on reset password page **/
    }


    /**
     * METHOD: post
     * URL: /reset
     * @param Request $request
     * @return Response
     */
    public function resetPassword(Request $request): Response
    {
        // TODO: we need to verify forgotPassword token that was sent by email
        try {
            $data = (new ResetPasswordRequestValidator())->attempt($request);
            $this->userService->resetPassword($data['email'], $data['password']);
            $token = $this->userService->createToken($data['email'], $data['password']);

        } catch (ValidationException $e) {
            return \response(['message' => $e->validator->errors()->first()], Response::HTTP_BAD_REQUEST);

        } catch (ModelNotFoundException $e) {
            return \response(['message' => 'User not exist.'], Response::HTTP_NOT_FOUND);

        } catch (JWTException | Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_I_AM_A_TEAPOT);
        }

        return \response(['token' => $token]);
    }


    /**
     * METHOD: get
     * URL: /refresh-token
     * @return Response
     */
    public function refreshToken(): Response
    {
        try {
        $token = \JWTAuth::refresh(\JWTAuth::getToken());
            } catch (Throwable $e) {
                return \response(['message' => $e->getMessage()], Response::HTTP_I_AM_A_TEAPOT);
            }

        return \response(['token' => $token]);
    }


    /**
     * Send email after registration
     * @param string $reason
     * @param string $email
     * @param string $clientUrl
     * @return Response
     */
    protected function sendEmail(string $reason, string $email, string $clientUrl = null): Response
    {
        try {
            $this->userService->sendEmailWithToken($reason, $email, $clientUrl);
        } catch (BadRequestHttpException $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_I_AM_A_TEAPOT);
        }

        return \response(['message' => 'The invitation token has been sent! Please check your email.']);
    }


    /**
     * METHOD: get
     * URL: /confirm
     * @param Request $request
     * @return Response
     */
    public function confirmRegister(Request $request): Response
    {
        try {
            $data = app(ConfirmRegisterRequestValidator::class)->attempt($request);
            $user = $this->userService->confirmUser($data['token']);
            $token = $this->userService->createToken($user->email);

        } catch (ValidationException $e) {
            return \response(['message' => $e->validator->errors()->first()], Response::HTTP_BAD_REQUEST);

        } catch (ModelNotFoundException $e) {
            return \response(['message' => 'User not found'], Response::HTTP_NOT_FOUND);

        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_I_AM_A_TEAPOT);
        }

        return \response(['token' => $token, 'user' => UserResource::make($user)]);
    }
}
