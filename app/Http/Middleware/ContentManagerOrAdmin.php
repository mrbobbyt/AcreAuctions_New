<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Response;
use JWTAuth;

class ContentManagerOrAdmin
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
        $contentManager = JWTAuth::user()->getJWTIdentifier() !== $id;
        $notAdmin = in_array(JWTAuth::user()->role, [User::ROLE_ADMIN, User::ROLE_CONTENT_MANAGER]);
        if ($contentManager && !$notAdmin) {
            return response(['message' => 'Permission denied.'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
