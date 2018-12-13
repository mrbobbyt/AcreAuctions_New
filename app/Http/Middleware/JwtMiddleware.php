<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use JWTAuth;
use \Tymon\JWTAuth\Exceptions\TokenInvalidException;
use \Tymon\JWTAuth\Exceptions\TokenExpiredException;


class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            // check if token is valid
            $token = JWTAuth::parseToken();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return abort(400, 'Token is Invalid');
            } else if ($e instanceof TokenExpiredException) {
                return abort(400, 'Token is Expired');
            } else {
                return abort(403, 'Authorization Token not found');
            }
        }

        return $next($request);
    }
}
