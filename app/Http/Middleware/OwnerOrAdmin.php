<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Response;
use JWTAuth;

class OwnerOrAdmin
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
        $id = (int)$request->route('id');
        $ownedByOtherUser = JWTAuth::user()->getJWTIdentifier() !== $id;
        $notAdmin = JWTAuth::user()->role !== User::ROLE_ADMIN;

        if ($ownedByOtherUser && $notAdmin) {
            return response(['message' => 'Permission denied.'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
