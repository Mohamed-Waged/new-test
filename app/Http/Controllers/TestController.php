<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Mail\TestMailable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class TestController extends Controller
{
    /**
     * @param string $fcmToken
     * @return JsonResponse
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function testPushNotification($fcmToken): JsonResponse
    {
        try {

            if (!empty($fcmToken)) {
                $response = FCMService::send(
                    $fcmToken,
                    [
                        'title' => 'Title goes here..',
                        'body' => 'Body goes here..'
                    ]
                );
            }

            $data = ['message' => $response];
            return response()->json(['data' => $data], 200);
        } catch (Exception $e) {
            $data = ['message' => $e->getMessage()];
            return response()->json(['data' => $data], 500);
        }
    }

    /**
     * @param string $email
     * @return JsonResponse
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function testSendEmail($email): JsonResponse
    {
        try {
            $user = User::where('email', $email)->first();
            if ($user) {
                $response = Mail::to($email)->send(new TestMailable($user));
            }

            $data = ['message' => $response];
            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            $data = ['message' => $e->getMessage()];
            return response()->json(['data' => $data], 500);
        }
    }

    /**
     * @param string $email
     * @return Renderable
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function testPreviewEmail($email)
    {
        $user = User::where('email', $email)->first();
        return view('emails.en.test', compact('user'));
    }
}
