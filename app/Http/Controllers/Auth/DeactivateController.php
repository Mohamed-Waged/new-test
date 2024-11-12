<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class DeactivateController extends Controller
{
    /**
     * Deactivate User.
     *
     * @return JsonResponse
     */
    public function deactivate(): JsonResponse
    {
        $user = auth('api')->user();

        if (!$user) {
            return $this->respondWithError(trans('auth.userNotFound'), Response::HTTP_NOT_FOUND);
        }

        if ($user->is_active === false) {
            return $this->respondWithError(trans('auth.userAlreadyDeactivted'), Response::HTTP_FORBIDDEN);
        }

        $user->update(['is_active' => false]);

        return $this->respondWithSuccess(trans('auth.userDeactivtedSuccess'));
    }
}
