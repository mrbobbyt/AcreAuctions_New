<?php
declare(strict_types = 1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\PayloadException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenInvalidException | PayloadException $e) {
            return response(['message' => 'Token is Invalid'], Response::HTTP_UNAUTHORIZED);
        } catch (JWTException | Throwable $e) {
            return response(['message' => 'Authorization Token not found'], Response::HTTP_UNAUTHORIZED);
        }

        if ($user->email_verified_at === null) {
            return response(
                ['message' => 'Registration is not completed. Please confirm your email.'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        return $next($request);
    }
}
