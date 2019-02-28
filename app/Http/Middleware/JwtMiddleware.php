<?php
declare(strict_types = 1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
            return response()->json([
                'status' => 'Error',
                'message' => 'Token is Invalid'
            ], 400);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Token is expired.'
            ], 400);
        } catch (JWTException | Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Authorization Token not found'
            ], 403);
        }

        if ($user->email_verified_at === null) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Registration is not completed. Please confirm your email.'
            ], 401);
        }

        return $next($request);
    }
}
