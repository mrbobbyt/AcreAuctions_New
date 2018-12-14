<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use JWTAuth;
use \Tymon\JWTAuth\Exceptions\TokenInvalidException;
use \Tymon\JWTAuth\Exceptions\TokenExpiredException;


class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // check if token is valid
            $token = JWTAuth::parseToken();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Token is Invalid'
                ], 400);
            } else if ($e instanceof TokenExpiredException) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Token is Expired'
                ], 400);
            } else {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Authorization Token not found'
                ], 403);
            }
        }

        return $next($request);
    }
}
