<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Services\Auth\Validators\RegisterRequestUserServiceValidator;
use App\Services\Auth\Validators\ResetPasswordRequestValidator;
use Illuminate\Http\Request;
use App\Services\Auth\Contracts\UserServiceContract;
use App\Http\Resources\UserResource;

use Throwable;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\ValidationException;
use App\Services\Auth\Validators\LoginRequestUserServiceValidator;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserServiceContract $userService)
    {
        $this->userService = $userService;
    }


    /**
     * METHOD: post
     * URL: /api/login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $data = (new LoginRequestUserServiceValidator())->attempt($request);
            $token = $this->userService->getToken($data['body']);

            if (!$token) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'The email or the password is wrong.',
                ], 400);
            }
        } catch (JWTException $e) {
            return abort(500, 'Sorry, could not create token.');
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (Throwable $e) {
            return abort(500, 'Sorry, the user could not login.');
        }

        return response()->json(compact('token'));
    }


    /**
     * METHOD: post
     * URL: /api/register
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
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
            return abort(500, 'Sorry, could not create token.');
        } catch (Throwable $e) {
            return abort(500, 'Sorry, the user could not register.');
        }

        return response()->json(compact('token'));
    }


    /**
     * METHOD: get
     * URL: /api/logout
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            $this->userService->breakToken();

        } catch (JWTException $e) {
            return abort(401, $e->getMessage());
        } catch (Throwable $e) {
            return abort(500, 'Sorry, the user cannot be logged out.');
        }

        return response()->json(['User logged out successfully.']);
    }

    public function forgotPassword()
    {

    }


    /**
     * METHOD: post
     * URL: /api/reset
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {

        try {
            $data = app(ResetPasswordRequestValidator::class)->attempt($request);
            $tokenOld = $this->userService->getResetToken($data['body']);

            // When email + pass does not match with user
            if (!$tokenOld) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'The current password is wrong.',
                ], 400);
            }

            $this->userService->resetPassword($data['body']);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (JWTException $e) {
            return abort(401, $e->getMessage());
        } catch (Throwable $e) {
            return abort(401, $e->getMessage());
        }

//        have to sent new token
//        maybe need to sent email with access token
//        return response()->json(['The reset password has been sent! Please check your email.']);

        try {
            $token = $this->userService->getToken($data['body']);
        } catch (JWTException $e) {
            return abort(401, $e->getMessage());
        }

        return response()->json(compact('token'));
    }


    /**
     * Return auth user profile
     *
     * METHOD: get
     * URL: /api/profile
     *
     * @return UserResource
     */
    public function profile(): UserResource
    {
        try {
            $user = $this->userService->authenticate();

            if (!$user) {
                return abort(404, "User not found.");
            }

        } catch (TokenExpiredException $e) {
            return abort(400, $e->getMessage());
        } catch (TokenInvalidException $e) {
            return abort(400, $e->getMessage());
        } catch (JWTException $e) {
            return abort(403, $e->getMessage());
        }

        return UserResource::make($user);
    }
}
