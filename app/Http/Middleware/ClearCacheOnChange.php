<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\ResponseCache\Facades\ResponseCache;

class ClearCacheOnChange
{
    protected $dontClearCache;

    public function __construct()
    {
        $this->dontClearCache = [
            '/api/v1/auth/login',
            '/api/v1/auth/register',
        ];
    }

    /**
     * Handle an incoming request and clear the cache if the request is not a GET method
     * and the request URI is not in the list of URIs that should not clear the cache.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (
            strtoupper($request->method()) !== "GET" &&
            !in_array($request->getRequestUri(), $this->dontClearCache)
        ) {
           ResponseCache::clear();
        }

        return $next($request);
    }
}
