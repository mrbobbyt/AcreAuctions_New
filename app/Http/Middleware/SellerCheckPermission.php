<?php
declare(strict_types = 1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class SellerCheckPermission
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
        $id = $request->route('id');

        try {
            $userId = \App\Models\Seller::query()->findOrFail($id)->user_id;
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Seller not exists.'
            ], 403);
        }

        if ($userId === JWTAuth::user()->getJWTIdentifier() ||
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
