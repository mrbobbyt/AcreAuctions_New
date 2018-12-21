<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;

use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\Auth\Contracts\UserAuthServiceContract;
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
     *
     * METHOD: get
     * URL: /api
     *
     * @throws FacebookSDKException
     * @throws Throwable
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Handle request from socials
        if (request('code')) {
            $this->handleSocialRequest();
        }

        // Get url to socials login
        try {
            $loginUrlFb = $this->fbService->getLogin();
            $loginUrlGoogle = $this->googleService->getLogin();
        } catch (FacebookSDKException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage(),
            ], $e->getCode());
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage(),
            ], $e->getCode());
        }

        return view('test-login', [
            'loginUrlFb' => $loginUrlFb,
            'loginUrlGoogle' => $loginUrlGoogle
        ]);
    }


    /**
     * METHOD: post
     * URL: /api/login
     *
     * @param Request $request
     * @throws ValidationException
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $data = app(LoginRequestUserServiceValidator::class)->attempt($request);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Sorry, the user could not login.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'token' => $data['token'],
            'user' => UserResource::make($data['user'])
        ]);
    }


    /**
     * METHOD: post
     * URL: /api/register
     *
     * @param Request $request
     * @throws ValidationException
     * @throws Throwable
     * @return array
     */
    public function handleForm(Request $request): array
    {
        try {
            $data = (new RegisterRequestUserServiceValidator())->attempt($request);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Sorry, the user could not register.'
            ], 500);
        }

        return $data;
    }


    /**
     * Handle request from social networks
     *
     * @throws FacebookResponseException
     * @throws FacebookSDKException
     * @throws Throwable
     * @throws ValidationException
     * @throws JWTException
     */
    public function handleSocialRequest()
    {
        if (request('scope')) {
            $data = $this->handleGoogle();
        } elseif (request('state')) {
            $data = $this->handleFacebook();
        }

        if (empty($data)) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Sorry, can not register user via social network',
            ], 401);
        }

        // check if user exist
        if ($this->userRepo->checkUserExists($data['body']['email'])) {

            // try to login user
            try {
                $user = $this->userRepo->findByEmail($data['body']['email']);
                $token = $this->userService->createToken($user);
            } catch (JWTException $e) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Sorry, could not create token.'
                ], 500);
            } catch (Throwable $e) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Sorry, the user could not register.'
                ], 500);
            }

            return response()->json([
                'status' => 'Success',
                'token' => $token,
                'user' => UserResource::make($user)
            ]);

        }

        $this->register($data);
    }


    /**
     * Get validated user data from Google
     *
     * @throws FacebookSDKException
     * @throws FacebookResponseException
     * @throws ValidationException
     * @throws Throwable
     * @return array
     */
    public function handleFacebook(): array
    {
        try {
            $client = $this->fbService->getProfile();
            $data = (new FacebookRequestValidator())->attempt($client);

        } catch(FacebookResponseException $e) {
            // When Graph returns an error
            return response()->json([
                'status' => 'Error',
                'message' => 'Graph returned an error: ' . $e->getMessage(),
            ], $e->getCode());
        } catch (FacebookSDKException $e) {
            // When validation fails or other local issues
            return response()->json([
                'status' => 'Error',
                'message' => 'Facebook SDK returned an error: ' . $e->getMessage(),
            ], $e->getCode());
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage(),
            ], $e->getCode());
        }

        return $data;
    }


    /**
     * Get validated user data from Google
     *
     * @throws ValidationException
     * @throws Throwable
     * @return array
     */
    public function handleGoogle(): array
    {
        try {
            $client = $this->googleService->getProfile();
            $data = (new GoogleRequestValidator())->attempt($client);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 500);
        }

        return $data;
    }


    /**
     * METHOD: post
     * URL: /api/register
     *
     * @param array $data
     * @throws JWTException
     * @throws Throwable
     * @return JsonResponse
     */
    public function register(array $data): JsonResponse
    {
        try {
            $user = $this->userService->create($data['body']);
            $token = $this->userService->createToken($user);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Sorry, could not create token.'
            ], 500);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Sorry, the user could not register.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'token' => $token,
        ]);
    }


    /**
     * METHOD: get
     * URL: /api/logout
     *
     * @throws JWTException
     * @throws Throwable
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            $this->userService->breakToken();

        } catch (JWTException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 401);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Sorry, the user cannot be logged out.'
            ], 500);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'User logged out successfully.'
        ]);
    }


    /**
     * METHOD: post
     * URL: /api/forgot
     *
     * @param Request $request
     * @throws ValidationException
     * @throws Throwable
     * @return JsonResponse
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $data = (new ForgotRequestUserServiceValidator())->attempt($request);
            $this->userService->sendEmailWithToken($data);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
                ], 401);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'The invitation token has been sent! Please check your email.'
        ]);

        /*
         * when user went on link with received token - redirect him on reset password page
         *
         * */

    }


    /**
     * METHOD: post
     * URL: /api/reset
     *
     * @param Request $request
     * @throws ValidationException
     * @throws JWTException
     * @throws Throwable
     * @return JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $data = (new ResetPasswordRequestValidator())->attempt($request);
            $this->userService->resetPassword($data['body']);
            $token = $this->userService->getToken($data['body']);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
                ], 401);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 500);
        }

//        have to sent new token
//        maybe need to sent email with access token
//        return response()->json(['The reset password has been sent! Please check your email.']);

        return response()->json([
            'status' => 'Success',
            'token' => $token
        ]);
    }

}
