<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JWTAuth;

class Owner
{
    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param  Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $id = (int)$request->route('id');

        if (JWTAuth::user()->getJWTIdentifier() !== $id) {
            return response(['message' => 'Permission denied.'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
