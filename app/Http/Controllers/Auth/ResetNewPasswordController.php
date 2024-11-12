<?php

namespace App\Http\Controllers\Auth;

use Helper;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetNewPasswordRequest;
use Illuminate\Support\Facades\Hash;

class ResetNewPasswordController extends Controller
{
    /**
     * Update the user's password.
     *
     * @param ResetNewPasswordRequest $request
     * @return JsonResponse
     */
    public function resetNewPassword(ResetNewPasswordRequest $request): JsonResponse
    {
        // Retrieve user by email
        $user = User::where('email', $request->email)->firstOrFail();

        if (!$user) {
            return $this->respondWithError(trans('auth.userNotFound'), Response::HTTP_NOT_FOUND);
        }

        // Validate the provided validation code
        if (!$this->isValidationCodeValid($request->validation_code, $user->validation_code)) {
            return $this->respondWithError(trans('auth.validationCodeNotMatch'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Update user's password
        $this->updateUserPassword($user, $request->new_password);

        // Send reset email and push notification
        $this->sendNotifications($user);

        return $this->respondWithSuccess(trans('auth.passwordResetSuccess'));
    }

    /**
     * Validate the provided validation code.
     *
     * @param string $inputCode
     * @param string $actualCode
     * @return bool
     */
    private function isValidationCodeValid(string $inputCode, string $actualCode): bool
    {
        return $inputCode === $actualCode;
    }

    /**
     * Update the user's password.
     *
     * @param User $user
     * @param string $new_password
     * @return void
     */
    private function updateUserPassword(User $user, string $new_password): void
    {
        $user->password = Hash::make($new_password);
        $user->save();
    }

    /**
     * Send reset email and push notification.
     *
     * @param User $user
     * @return void
     */
    private function sendNotifications(User $user): void
    {
        Helper::sendEmailTemplate($user, 'reset');

        Helper::sendPushNotification($user, 'Reset Password!', 'Password reset successfully.');
    }
}
