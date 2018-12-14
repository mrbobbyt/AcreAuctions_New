<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;

use App\Services\Auth\Contracts\UserAuthServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

use App\Services\Auth\Validators\ForgotRequestUserServiceValidator;
use App\Services\Auth\Validators\RegisterRequestUserServiceValidator;
use App\Services\Auth\Validators\ResetPasswordRequestValidator;
use App\Services\Auth\Validators\LoginRequestUserServiceValidator;

use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    protected $userService;

    public function __construct(UserAuthServiceContract $userService)
    {
        $this->userService = $userService;
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
     * @throws JWTException
     * @throws Throwable
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $data = (new RegisterRequestUserServiceValidator())->attempt($request);
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
            'token' => $token
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
