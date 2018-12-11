<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use JWTAuth;

class UserController extends Controller
{

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
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return abort(404, "User not found");
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return abort(400, $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return abort(400, $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return abort(403, $e->getStatusCode());
        }

        //return response()->json(compact('user'));
        return UserResource::make($user);
    }
}
