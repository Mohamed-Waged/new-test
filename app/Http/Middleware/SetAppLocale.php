<?php

namespace App\Http\Middleware;

use App;
use Config;
use Closure;
use Illuminate\Http\Request;
use App\Constants\GlobalConstants;

class SetAppLocale
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Set default Content-Type if not present
        if (!$request->headers->has('Content-Type')) {
            $request->headers->set('Content-Type', GlobalConstants::DEFAULT_CONTENT_TYPE);
        }

        // Set the default guard for authentication
        Config::set('auth.defaults.guard', GlobalConstants::DEFAULT_GUARD);

        // Get and validate locale
        $locale = trim($request->header('locale') ?? $request->get('locale'));
        if ($locale && in_array($locale, GlobalConstants::SUPPORTED_LOCALES)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
