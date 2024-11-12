<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\FcmTokenRequest;
use Symfony\Component\HttpFoundation\Response;

class FcmTokenController extends Controller
{
    /**
     * Update FCM Token.
     *
     * @param FcmTokenRequest $request
     * @return JsonResponse
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function updateUserFcmToken(FcmTokenRequest $request): JsonResponse
    {
        $user = User::findOrFail(auth('api')->user()->id);

        if (!$user) {
            return $this->respondWithError(trans('auth.userNotFound'), Response::HTTP_NOT_FOUND);
        }

        return $this->updateFcmToken($user, $request->fcm_token);
    }

    /**
     * Update the FCM token for the authenticated user.
     *
     * @param User $user
     * @param string $fcmToken
     * @return JsonResponse
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    private function updateFcmToken(User $user, string $fcmToken): JsonResponse
    {
        $user->update(['fcm_token' => $fcmToken]);

        return $this->respondWithSuccess(trans('auth.fcmTokenUpdatedSuccess'));
    }
}
