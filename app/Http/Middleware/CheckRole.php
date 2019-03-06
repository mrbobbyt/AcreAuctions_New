<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use JWTAuth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param array $roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        foreach ($roles as $role) {
            if (JWTAuth::user()->getRoleName() === $role) {
                return $next($request);
            }
        }

        return response(['message' => 'Permission denied.'], Response::HTTP_FORBIDDEN);
    }
}
