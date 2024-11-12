<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Imageable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Auth\AuthUserResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Auth\UpdateProfileRequest;

class ProfileController extends Controller
{
    /**
     * Get the authenticated User.
     *
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function profile()
    {
        $user = Auth::user();

        if (!$user) {
            return $this->respondWithError(trans('auth.unauthorized'), Response::HTTP_UNAUTHORIZED);
        }

        if (!$user->is_active) {
            return $this->respondWithError(trans('auth.userNotVerified'), Response::HTTP_FORBIDDEN);
        }

        return response()->json([
            'data' => new AuthUserResource($user)
        ]);
    }

    /**
     * Update authenticated User.
     *
     * @param UpdateProfileRequest $request
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = User::where('id', auth('api')->user()->id)->first();

        if (!empty($request->name)) {
            $user->name = $request->name;
        }

        if (!empty($request->email)) {
            $user->email = $request->email;
        }

        if (!empty($request->image_base64)) {
            $image = Imageable::uploadImage($request->image_base64, 'users');
            $user->image()->delete();
            $user->image()->create(['url' => $image]);
        }

        if (!empty($request->country_code)) {
            $user->country_code = $request->country_code;
        }

        if (!empty($request->mobile)) {
            $user->mobile = $request->mobile;
        }

        $user->save();

        return response()->json([
            'data' => new AuthUserResource($user)
        ]);
    }
}
