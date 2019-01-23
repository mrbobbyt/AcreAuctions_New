<?php
declare(strict_types = 1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class SellerCheckVerification
{
    /**
     * Handle an incoming request.
     * Check if seller is not verified OR user is authenticate AND not an admin OR company head
     * @param  Request $request
     * @param  Closure $next
     * @return mixed
     * @throws JWTException
     */
    public function handle($request, Closure $next)
    {
        $slug = $request->route('slug');

        try {
            $seller = \App\Models\Seller::query()->where('slug', $slug)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Seller not exists.'
            ], 403);
        }

        if ($seller->is_verified ||
            ((bool)JWTAuth::check(JWTAuth::getToken()) &&
                (
                    (JWTAuth::parseToken()->authenticate()->role === \App\Models\User::ROLE_ADMIN) ||
                    $seller->user_id === JWTAuth::parseToken()->authenticate()->getJWTIdentifier()
                )
            )
        ) {
            return $next($request);
        }

        return response()->json([
            'status' => 'Error',
            'message' => 'Seller is not verified.'
        ], 403);
    }
}
