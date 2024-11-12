<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\DB;
use Closure;
use JWTAuth;
use App\Models\User;

class CheckApiToken
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
        $authorizationHeader = trim($request->header('Authorization'));

        // Check if Authorization header is present
        if (empty($authorizationHeader)) {
            return response()->json([
                'data' => [
                    'message' => trans('auth.unauthorized')
                ]
            ], 401);
        }

        // Get the authenticated user ID
        $userId = auth('api')->id();

        // Check if user exists and token is valid
        if ($userId && $this->isTokenValid($userId)) {
            return $next($request);
        }
    }

    /**
     * Check if the token is valid for the user.
     *
     * @param  int  $userId
     * @return bool
     */
    private function isTokenValid(int $userId): bool
    {
        $token = JWTAuth::getToken();

        return DB::table('personal_access_tokens')
            ->where('tokenable_type', 'Bearer')
            ->where('tokenable_id', $userId)
            ->where('token', $token)
            ->exists();
    }
}
