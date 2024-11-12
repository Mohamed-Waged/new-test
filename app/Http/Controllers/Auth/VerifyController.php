<?php

namespace App\Http\Controllers\Auth;

use Helper;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyRequest;
use Illuminate\Contracts\Support\Renderable;
use Symfony\Component\HttpFoundation\Response;

class VerifyController extends Controller
{
    /**
     * Verify user email.
     *
     * @param VerifyRequest $request
     * @return JsonResponse
     */
    public function verify(VerifyRequest $request): JsonResponse
    {
        $user = $this->getUserByEmail($request->email);

        if (!$user) {
            return $this->respondWithError(trans('auth.userNotFound'), Response::HTTP_NOT_FOUND);
        }

        return $this->processUserVerification($request, $user);
    }

    /**
     * Verify user email.
     *
     * @param VerifyRequest $request
     * @return JsonResponse
     */
    public function processUserVerification(VerifyRequest $request, User $user): JsonResponse
    {
        if ($this->isUserAlreadyVerified($user)) {
            return $this->respondWithError(trans('auth.userAlreadyVerified'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!$this->isValidationCodeValid($request->validation_code, $user->validation_code)) {
            return $this->respondWithError(trans('auth.validationCodeNotMatch'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Active the user
        $this->activateUser($user);

        // Send welcome email and push notification
        $this->sendWelcomeNotifications($user);

        return $this->respondWithSuccess(trans('auth.userVerifiedSuccess'));
    }

    private function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->firstOrFail();
    }

    private function isUserAlreadyVerified(User $user): bool
    {
        return $user->is_active;
    }

    private function isValidationCodeValid(string $inputCode, ?string $storedCode): bool
    {
        return $inputCode === $storedCode;
    }

    private function activateUser(User $user): void
    {
        $user->update([
            'validation_code'   => null,
            'is_active'         => true,
        ]);

        $user->markEmailAsVerified();
    }

    private function sendWelcomeNotifications(User $user): void
    {
        Helper::sendEmailTemplate($user, 'welcome');
        Helper::sendPushNotification($user, 'Welcome onboard!');
    }

    /**
     * @param string $validationCode
     * @return Renderable
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function verifyUserByEmail($validationCode): Renderable
    {
        try {
            $user = User::where('validation_code', $validationCode)->first();
            if (!$user->is_active) {
                $user->is_active = true;
                $user->save();

                return view('verifiedSuccess');
            } else {
                return view('verifiedError');
            }
        } catch (Exception $e) {
            return view('verifiedError');
        }
    }
}
