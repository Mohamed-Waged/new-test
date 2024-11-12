<?php

namespace App\Http\Controllers\Auth;

use Helper;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RequestNewCodeRequest;
use Symfony\Component\HttpFoundation\Response;

class RequestNewCodeController extends Controller
{
    /**
     * Request new valiation code.
     *
     * @param RequestNewCodeRequest $request
     * @return JsonResponse
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function requestNewCode(RequestNewCodeRequest $request): JsonResponse
    {
        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if user exists
        if (!$user) {
            return $this->respondWithError(trans('auth.userNotFound'), Response::HTTP_NOT_FOUND);
        }

        // Check if the user is already verified
        if ($user->is_active) {
            return $this->respondWithError(trans('auth.userAlreadyVerified'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Generate a new validation code
        $user->validation_code = $this->generateValidationCode();

        // Save the user with the new validation code
        $user->save();

        // Send verification email and push notification
        $this->sendNotifications($user);

        return $this->respondWithSuccess(trans('auth.requestNewCodeSuccess'));
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

    /**
     * Send notifications for the new validation code.
     *
     * @param User $user
     * @return void
     */
    private function sendNotifications(User $user): void
    {
        // Send verification email
        Helper::sendEmailTemplate($user, 'verify');

        // Send push notification
        Helper::sendPushNotification($user, 'Request New Code!', "New Code: {$user->validation_code}");
    }
}
