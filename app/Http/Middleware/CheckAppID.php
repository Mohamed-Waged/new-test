<?php

namespace App\Http\Middleware;

use Helper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Constants\GlobalConstants;

class CheckAppID
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function handle(Request $request, Closure $next)
    {
        $appId = trim($request->header('appId'));

        if (!$this->isAppIdDefined($appId)) {
            return $this->respondWithError(GlobalConstants::UNDEFINED_APP_ID_MESSAGE);
        }
        
        if (!$this->isAppIdRegistered($appId)) {
            return $this->respondWithError(GlobalConstants::UNREGISTERED_APP_ID_MESSAGE);
        }
        if (!$this->isDomainRegistered($request->root())) {
            return $this->respondWithError(GlobalConstants::UNREGISTERED_DOMAIN_MESSAGE);
        }

        return $next($request);
    }

    private function isAppIdDefined(?string $appId): bool
    {
        return !empty($appId);
    }

    private function isAppIdRegistered(string $appId): bool
    {
        return in_array($appId, Helper::getAvailableAppIds());
    }

    private function isDomainRegistered(string $domain): bool
    {
        return in_array($domain, GlobalConstants::REGISTERED_DOMAIN_NAME);
    }

    private function respondWithError(string $message): JsonResponse
    {
        return response()->json(['data' => ['message' => $message]], 422);
    }
}
