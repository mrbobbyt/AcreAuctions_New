<?php
declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use JWTAuth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (JWTAuth::user() && JWTAuth::user()->role === User::ROLE_ADMIN) {
            return $next($request);
        }

        return response()->json([
            'status' => 'Error',
            'message' => 'You have no permission.'
        ], 403);

    }
}
