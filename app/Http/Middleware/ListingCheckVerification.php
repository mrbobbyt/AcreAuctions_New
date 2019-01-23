<?php
declare(strict_types = 1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class ListingCheckVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param  Closure $next
     * @return mixed
     * @throws JWTException
     */
    public function handle($request, Closure $next)
    {
        $slug = $request->route('slug');

        try {
            $listing = \App\Models\Listing::query()->where('slug', $slug)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Listing not exists.'
            ], 403);
        }

        if ($listing->is_verified ||
            ((bool)JWTAuth::check(JWTAuth::getToken()) &&
                (
                    (JWTAuth::parseToken()->authenticate()->role === \App\Models\User::ROLE_ADMIN) ||
                    $listing->seller->user_id === JWTAuth::parseToken()->authenticate()->getJWTIdentifier()
                )
            )
        ) {
            return $next($request);
        }

        return response()->json([
            'status' => 'Error',
            'message' => 'Listing is not verified.'
        ], 403);
    }
}
