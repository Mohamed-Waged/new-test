<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\AuthTokenResource;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    const ROLE_MEMBER = 'member';

    const SINGLE_DEVICE_BY_USER = false;

    /**
     * login
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $token = auth('api')->attempt($credentials);

        if (!$token) {
            return $this->respondWithError(trans('auth.unauthorized'), Response::HTTP_UNAUTHORIZED);
        }

        $user = auth('api')->user();

        return $this->checkUserStatus($user, $token);
    }

    /**
     * checkUserStatus
     *
     * @param mixed $user
     * @param mixed $token
     * @return JsonResponse
     */
    private function checkUserStatus($user, $token): JsonResponse
    {
        if ($user->suspend === true) {
            return $this->respondWithError(trans('auth.userSuspended'), Response::HTTP_FORBIDDEN);
        }

        if ($user->is_active === false) {
            return $this->respondWithError(trans('auth.userInactive'), Response::HTTP_FORBIDDEN);
        }

        return $this->respondWithToken($token, $user);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     * @param mixed $user
     * @return JsonResponse
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    protected function respondWithToken($token, $user): JsonResponse
    {
        $user['token'] = $token;

        if (self::SINGLE_DEVICE_BY_USER) {
            DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();
        }

        // register accessToken JWT in DB till they fix it, by deleteing or expire old tokens
        DB::table('personal_access_tokens')
            ->insert([
                'tokenable_type' => 'Bearer',
                'tokenable_id'   => $user->id,
                'ip_address'     => request()->ip(),
                'token'          => $token,
                'last_used_at'   => Carbon::now()
            ]);

        return response()->json([
            'data' => new AuthTokenResource($user)
        ]);
    }

    /**
     * logout
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return $this->respondWithSuccess(trans('auth.userLoggedOut'));
        } catch (JWTException) {
            return $this->respondWithError(trans('auth.userLoggedOut'), Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function refresh(): JsonResponse
    {
        $token = auth('api')->refresh();
        return $this->respondWithToken($token, auth('api')->user());
    }
}
