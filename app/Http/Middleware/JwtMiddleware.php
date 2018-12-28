<?php
declare(strict_types = 1);

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use JWTAuth;
use \Tymon\JWTAuth\Exceptions\TokenInvalidException;
use \Tymon\JWTAuth\Exceptions\TokenExpiredException;


class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenInvalidException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Token is Invalid'
            ], 400);
        } catch (TokenExpiredException $e) {
            try {
                $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                $request->headers->set('Authorization', 'Bearer ' . $refreshed);
            } catch (JWTException $e) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Token is not refreshable.'
                ], 400);
            }
            $user = JWTAuth::setToken($refreshed)->toUser();
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Authorization Token not found'
            ], 403);
        }


        /*catch (Exception $e) {
            dd($e->getMessage());
            if ($e instanceof TokenInvalidException) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Token is Invalid'
                ], 400);
            } else if ($e instanceof TokenExpiredException) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'sdsdsd'
                ], 400);
                //JWTAuth::refresh(JWTAuth::check(JWTAuth::getToken()));
            } else {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Authorization Token not found'
                ], 403);
            }
        }*/

        return $next($request);
    }
}
