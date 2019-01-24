<?php
declare(strict_types = 1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserCheckRole
{
    /**
     * Handle an incoming request.
     * @param  Request $request
     * @param  Closure $next
     * @return mixed
     * @throws JWTException
     */
    public function handle($request, Closure $next)
    {
        if (JWTAuth::parseToken()->authenticate()->role ==
            (\App\Models\User::ROLE_ADMIN || \App\Models\User::ROLE_SELLER)
        ) {
            return $next($request);
        }

        return response()->json([
            'status' => 'Error',
            'message' => 'You have no permission.'
        ], 403);
    }
}
