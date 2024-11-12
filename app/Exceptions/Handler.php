<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Exceptions\CustomException;
use Throwable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof RouteNotFoundException) {
            $data = ['message' => 'The specified URL cannot be found.'];
            return response()->json(['data' => $data], 404);
        }

        if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
            $data = ['message' => 'User does not have the permission.'];
            return response()->json(['data' => $data], 403);
        }

        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            $data = ['message' => 'No results found.'];
            return response()->json(['data' => $data], 404);
        }

        if ($exception instanceof \Illuminate\Contracts\Encryption\DecryptException) {
            $data = ['message' => 'No results found.'];
            return response()->json(['data' => $data], 404);
        }

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            $data = ['message' => 'Method not allowed.'];
            return response()->json(['data' => $data], 503);
        }

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            $data = ['message' => 'Url not found.'];
            return response()->json(['data' => $data], 404);
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $data = ['message' => $exception->validator->errors()->first()];
            return response()->json(['data' => $data], 422);
        }

        if (env('APP_ENV') !== 'production') {
            $data = [
                'message' =>
                    $exception->getMessage() . ', at path ' .
                    $exception->getFile() . ', line ' .
                    $exception->getLine()
            ];
            return response()->json(['data' => $data], 500);
        } else {
            $data = ['message' => 'Something went wrong!'];
            return response()->json(['data' => $data], 500);
        }
    }
}
