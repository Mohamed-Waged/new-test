<?php

namespace App\Http\Controllers\Auth;

use Helper;
use Exception;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\RegisterRequest;
use LDAP\Result;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    const ROLE_MEMBER = 'member';

    private const VALIDATION_CODE_MIN = 1000;
    private const VALIDATION_CODE_MAX = 9999;

    /**
     * Register a new user.
     * @param RegisterRequest $request
     * @thrown Log Exception
     * @return JsonResponse
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = new User();
            $user->fill([
                'ip_address'        => $request->ip(),
                'user_agent'        => $request->header('User-Agent'),
                'name'              => $request->name,
                'email'             => strtolower($request->email),
                'country_code'      => $request->country_code,
                'mobile'            => $request->mobile,
                'fcm_token'         => $request->fcm_token,
                'password'          => Hash::make($request->password),
                'validation_code'   => random_int(self::VALIDATION_CODE_MIN, self::VALIDATION_CODE_MAX),
            ]);
            $user->save();

            $user->assignRole(self::ROLE_MEMBER); // assign role

            // Optionally, you can uncomment this line to trigger the registered event
            // event(new Registered($user));

            $this->sendVerifyNotifications($user);

            return $this->respondWithSuccess(trans('auth.userRegisteredSuccess'), Response::HTTP_CREATED);

        } catch (Exception $e) {
            Log::error('Error New Registration ' . $e->getMessage());

            return $this->respondWithError(trans('auth.registrationFailed'), Response::HTTP_SERVICE_UNAVAILABLE);
        }
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

    private function sendVerifyNotifications(User $user): void
    {
        // Send verification email
        Helper::sendEmailTemplate($user, 'verify');

        // Send push notification
        Helper::sendPushNotification($user, 'Verify your account!', "Code: {$user->validation_code}");
    }

}
