<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;

class UpdatePasswordController extends Controller
{
    /**
     * update password.
     * @param UpdatePasswordRequest $request
     * @return JsonResponse
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = Auth::user();

        if (!Hash::check($request->currentPassword, $user->password)) {
            return $this->respondWithError(trans('auth.validationCodeNotMatch'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->password = bcrypt($request->newPassword);
        $user->save();

        return $this->respondWithSuccess(trans('auth.passwordUpdatedSuccess'));
    }
}
