<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param Request $request
     * @return mixed
     */
    protected function redirectTo($request)
    {
        if (!auth()->guard('api')->check()) {
            return response()->json([
                'data' => [
                    'message' => trans('auth.unauthorized')
                ]
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $request;
    }
}
