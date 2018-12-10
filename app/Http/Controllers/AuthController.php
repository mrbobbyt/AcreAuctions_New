<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\User\Contracts\UserServiceContract;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

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
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return abort(401, 'invalid credentials');
            }
        } catch (JWTException $e) {
            return abort(500, 'could not create token');
        }
        return response()->json(compact('token'));
    }


    /**
     * METHOD: post
     * URL: /api/register
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->userService->create($request->validated());

        if ($user === false) {
            return abort(422, 'failed to create new user');
        }

        $token = JWTAuth::fromUser($user);

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
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['User logged out successfully']);
        } catch (JWTException $e) {
            return abort(500, 'Sorry, the user cannot be logged out');
        }

    }

    public function forgotPassword()
    {

    }

    public function resetPassword()
    {

    }
}
