<?php
declare(strict_types = 1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
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
        } catch (TokenInvalidException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Token is Invalid'
            ], 400);
        } catch (TokenExpiredException $e) {
            try {
                $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                $request->headers->set('Authorization', 'Bearer ' . $refreshed);
            } catch (JWTException $e) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Token is not refreshable.'
                ], 400);
            }
            $user = JWTAuth::setToken($refreshed)->toUser();
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Authorization Token not found'
            ], 403);
        }

        return $next($request);
    }
}
