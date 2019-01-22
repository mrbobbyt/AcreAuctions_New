<?php
declare(strict_types = 1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ListingCheckPermission
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @throws ModelNotFoundException
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $id = $request->route('id');

        try {
            $sellerId = \App\Models\Listing::query()->findOrFail($id)->seller->id;
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Listing not exists.'
            ], 403);
        }

        if (\JWTAuth::user()->seller->id === $sellerId ||
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
