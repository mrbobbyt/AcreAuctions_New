<?php
declare(strict_types = 1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use JWTAuth;

class UserCheckPermission
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
        $id = (int)$request->route('id');

        try {
            \App\Models\User::query()->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User not exists.'
            ], 403);
        }

        if ($id === JWTAuth::user()->getJWTIdentifier() ||
            (JWTAuth::user()->role === \App\Models\User::ROLE_ADMIN)
        ) {
            return $next($request);
        }

        return response()->json([
            'status' => 'Error',
            'message' => 'You have no permission.'
        ], 403);
    }
}
