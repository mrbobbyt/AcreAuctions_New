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
            $userRole = JWTAuth::user()->userRole;

            if ($userRole === null) { // todo
                return response(['message' => 'Permission denied.'], Response::HTTP_FORBIDDEN);
            }

            if ($userRole->name === $role) {
                return $next($request);
            }
        }

        return response(['message' => 'Permission denied.'], Response::HTTP_FORBIDDEN);
    }
}
