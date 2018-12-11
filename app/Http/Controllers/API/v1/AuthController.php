<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Services\Auth\Validators\RegisterRequestUserServiceValidator;
use Illuminate\Http\Request;
use App\Services\Auth\Contracts\UserServiceContract;
use App\Http\Resources\UserResource;

use JWTAuth;

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
            return abort(500, 'Could not create token.');
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        }
        catch (Throwable $e) {
            return abort(500, 'Something went wrong.');
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

            if ($user === false) {
                return abort(422, 'Failed to create new user.');
            }

            $token = $this->userService->createToken($user);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (Throwable $e) {
            return abort(500, 'Something went wrong.');
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

            return response()->json(['User logged out successfully.']);
        } catch (JWTException $e) {
            return abort(401, $e->getMessage());
        } catch (Throwable $e) {
            return abort(418, 'Sorry, the user cannot be logged out.');
        }

    }

    public function forgotPassword()
    {

    }

    public function resetPassword()
    {

    }


    /**
     * Return user profile
     *
     * METHOD: get
     * URL: /api/profile
     *
     * @return UserResource
     */
    public function profile(): UserResource
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

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
