<?php

namespace App\Http\Controllers\Auth;

use Helper;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgetPasswordRequest;

class ForgetPasswordController extends Controller
{
    /**
     * Forget Password.
     *
     * @param ForgetPasswordRequest $request
     * @return JsonResponse
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function forgetPassword(ForgetPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();

        if (!$user) {
            return $this->respondWithError(trans('auth.userNotFound'), Response::HTTP_NOT_FOUND);
        }

        return $this->processUserForget($user);
    }

    /**
     * Process User forget.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function processUserForget(User $user): JsonResponse
    {
        if ($this->isUserNotVerified($user)) {
            return $this->respondWithError(trans('auth.userNotVerified'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Generate a new validation code
        $user->validation_code = $this->generateValidationCode();

        // Save the user with the new validation code
        $user->save();

        // Send Push Notification
        $this->sendForgetNotifications($user);

        return $this->respondWithSuccess(trans('auth.forgetPassword'));
    }

    /**
     * Generate a random validation code.
     *
     * @return int
     */
    private function generateValidationCode(): int
    {
        return rand(1000, 9999);
    }

    private function isUserNotVerified(User $user): bool
    {
        return $user->is_active === false;
    }

    private function sendForgetNotifications(User $user): void
    {
        Helper::sendEmailTemplate($user, 'forget');

        Helper::sendPushNotification($user, 'Forget Password!', "Code : $user->validation_code");
    }
}
