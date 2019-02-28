<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\Auth\Contracts\UserAuthServiceContract;
use App\Services\Auth\Validators\ConfirmRegisterRequestValidator;
use App\Services\Social\Contracts\FacebookServiceContract;
use App\Services\Social\Contracts\GoogleServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

use App\Services\Auth\Validators\ForgotRequestUserServiceValidator;
use App\Services\Auth\Validators\RegisterRequestUserServiceValidator;
use App\Services\Auth\Validators\ResetPasswordRequestValidator;
use App\Services\Auth\Validators\LoginRequestUserServiceValidator;
use App\Services\Social\Validators\FacebookRequestValidator;
use App\Services\Social\Validators\GoogleRequestValidator;

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
    ) {
        $this->userService = $userService;
        $this->userRepo = $userRepo;
        $this->fbService = $fbService;
        $this->googleService = $googleService;
    }


    /**
     * Login page for social networks
     * METHOD: get
     * URL: /
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Get url to socials login
        try {
            $loginUrlFb = $this->fbService->getLogin();
            $loginUrlGoogle = $this->googleService->getLogin();
        } catch (FacebookSDKException $e) {
            // When Graph returns an error
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'loginUrlFb' => $loginUrlFb,
            'loginUrlGoogle' => $loginUrlGoogle
        ]);
    }

    
    /**
     * @return JsonResponse
     */
    public function handleSocials()
    {
        // Handle request from socials
        if (request('code') && request('scope')) {
            return $this->handleGoogle();
        } elseif (request('code') && request('state')) {
            return $this->handleFacebook();
        }
    }


    /**
     * METHOD: post
     * URL: /login
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $data = app(LoginRequestUserServiceValidator::class)->attempt($request);
            $token = $this->userService->createToken($data['body']);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User not exist.'
            ], 404);
        } catch (JWTException | Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Can not login.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'token' => $token,
            'user' => UserResource::make($data['user'])
        ]);
    }


    /**
     * METHOD: post
     * URL: /register
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $data = (new RegisterRequestUserServiceValidator())->attempt($request);
            $this->userService->create($data);
            return $this->sendEmail($data, 'register');

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (JWTException | Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Can not register.'
            ], 500);
        }
    }


    /**
     * Get validated user data from Google
     * @return JsonResponse
     */
    protected function handleFacebook(): JsonResponse
    {
        try {
            $client = $this->fbService->getProfile();
            $data = (new FacebookRequestValidator())->attempt($client);
            $user = $this->userService->createOrLogin($data);
            if (isset($user['register'])) {
                return $this->sendEmail($data, 'register');
            }

        } catch (FacebookResponseException | FacebookSDKException $e) {
            // When Graph returns an error
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage(),
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (JWTException | Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Can not register.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'token' => $user['token'],
            'user' => UserResource::make($user['user'])
        ]);
    }


    /**
     * Get validated user data from Google
     * @return JsonResponse
     */
    protected function handleGoogle(): JsonResponse
    {
        try {
            $client = $this->googleService->getProfile();
            $data = (new GoogleRequestValidator())->attempt($client);
            $user = $this->userService->createOrLogin($data);
            if (isset($user['register'])) {
                return $this->sendEmail($data, 'register');
            }

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (JWTException | Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Can not register.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'token' => $user['token'],
            'user' => UserResource::make($user['user'])
        ]);
    }


    /**
     * METHOD: get
     * URL: /logout
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            $this->userRepo->breakToken();

        } catch (JWTException | Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User logout error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'User logged out successfully.'
        ]);
    }


    /**
     * METHOD: post
     * URL: /forgot
     * @param Request $request
     * @return JsonResponse
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $data = (new ForgotRequestUserServiceValidator())->attempt($request);
            return $this->sendEmail($data, 'forgot');

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User action error.'
                ], 500);
        }

        /** when user went on link with received token - redirect him on reset password page **/
    }


    /**
     * METHOD: post
     * URL: /reset
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $data = (new ResetPasswordRequestValidator())->attempt($request);
            $this->userService->resetPassword($data['body']);
            $token = $this->userService->createToken($data['body']);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User not exist.'
            ], 404);
        } catch (JWTException | Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User action error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'token' => $token
        ]);
    }


    /**
     * METHOD: get
     * URL: /refresh-token
     * @return JsonResponse
     */
    public function refreshToken(): JsonResponse
    {
        try {
            $refreshed = \JWTAuth::refresh(\JWTAuth::getToken());

        } catch (JWTException | Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User login error.'
            ], 404);
        }

        return response()->json([
            'status' => 'Success',
            'token' => $refreshed
        ]);
    }


    /**
     * Send email after registration
     * @param $data
     * @param $reason
     * @return JsonResponse
     */
    protected function sendEmail($data, $reason): JsonResponse
    {
        try {
            $this->userService->sendEmailWithToken($data, $reason);

        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User action error.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'The invitation token has been sent! Please check your email.'
        ]);
    }


    /**
     * METHOD: get
     * URL: /confirm
     * @param Request $request
     * @return JsonResponse
     */
    public function confirmRegister(Request $request): JsonResponse
    {
        try {
            $data = app(ConfirmRegisterRequestValidator::class)->attempt($request);
            $user = $this->userService->confirmUser($data['body']);
            $data['body']['email'] = $user->email;
            $token = $this->userService->createToken($data['body']);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User not exist.'
            ], 404);
        } catch (JWTException | Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Can not login.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'token' => $token,
            'user' => UserResource::make($user)
        ]);
    }
}
