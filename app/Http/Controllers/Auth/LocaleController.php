<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\Auth\LocaleRequest;

class LocaleController extends Controller
{
    /**
     * Update user Locale.
     *
     * @param LocaleRequest $request
     * @return JsonResponse
     */
    public function updateUserLocale(LocaleRequest $request): JsonResponse
    {
        $user = User::findOrFail(auth('api')->user()->id);

        if (!$user) {
            return $this->respondWithError(trans('auth.userNotFound'), Response::HTTP_NOT_FOUND);
        }

        // Update the user's locale
        return $this->updateUserLocaleInDatabase($user, $request->locale);
    }

    /**
     * Update user's locale in the database.
     *
     * @param User $user
     * @param string $locale
     */
    private function updateUserLocaleInDatabase(User $user, string $locale)
    {
        $user->update(['locale' => $locale]);

        return $this->respondWithSuccess(trans('auth.localeUpdatedSuccess'));
    }
}
