<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function formatValidationErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }

    protected function respondWithSuccess($message, $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json(['data' => ['message' => $message]], $statusCode);
    }

    protected function respondWithError($message, $statusCode): JsonResponse
    {
        return response()->json(['data' => ['message' => $message]], $statusCode);
    }
}
