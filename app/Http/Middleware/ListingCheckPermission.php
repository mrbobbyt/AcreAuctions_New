<?php
declare(strict_types = 1);

namespace App\Http\Middleware;

use Closure;

class ListingCheckPermission
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
        $id = $request->route('id');
        if (\JWTAuth::user()->seller->id === \App\Models\Listing::query()->findOrFail($id)->seller->id ||
            \JWTAuth::user()->role === \App\Models\User::ROLE_ADMIN
        ) {
            return $next($request);
        }

        return response()->json([
            'status' => 'Error',
            'message' => 'You have no permission.'
        ], 403);
    }
}
