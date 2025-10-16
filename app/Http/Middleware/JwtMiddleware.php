<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            // This will throw exception if token is invalid, expired, or missing
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return response()->json([
                 "response" => [
                'message' => 'Token expired',
                'status'  => 401
                 ]
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                 "response" => [
                'message' => 'Token invalid',
                'status'  => 401
                 ]
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                 "response" => [
                'message' => 'Token not provided',
                'status'  => 401
                 ]
            ], 401);
        }

        return $next($request);
    }
}
