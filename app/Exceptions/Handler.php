<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponser;

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                $msgError = ['message' => 'The GET method is not supported for this route. Supported methods: POST.'];
                return $this->responseError($msgError, 'METHOD_IS_NOT_SUPPORT', 405);
            }
        });

        /* 401 message */
        $this->renderable(function (AuthenticationException $e, Request $request) {
            /* This is authorized by logged in users */
            if ($request->expectsJson()) {
                $msgError = ['message' => 'This action is unauthorized'];
                return $this->responseError($msgError, 'UNAUTHORIZED_ACTION', 401);
            }
        });

        /* 403 message */
        $this->renderable(function (UnauthorizedException $e, Request $request) {
            /* This is authorized by roles or permisions assigned to users */
            if ($request->expectsJson()) {
                $msgError = ['message' => 'This action is unauthorized'];
                return $this->responseError($msgError, 'UNAUTHORIZED_ACTION', 403);
            }
        });

        /* 403 message */
        $this->renderable(function (AccessDeniedHttpException $e, Request $request) {
            /* This is authorized by request class */
            if ($request->expectsJson()) {
                $msgError = ['message' => 'Permission Denied'];
                return $this->responseError($msgError, 'PERMISSION_DENIED', 403);
            }
        });

        /* 400 message */
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                $msgError = ['message' => 'Page Not Found'];
                return $this->responseError($msgError, 'PAGE_NOT_FOUND');
            }
        });

        /* 404 message */
        // $this->renderable(function(NotFoundHttpException $e, Request $request) {
        //     if ($request->expectsJson()) {
        //         $msgError = ['message' => 'Page Not Found'];
        //         return $this->responseError($msgError, 404);
        //     }
        // });

        /* 429 message */
        $this->renderable(function (ThrottleRequestsException $e, Request $request) {
            if ($request->expectsJson()) {
                $msgError = ['message' => 'Too Many Attempts'];
                return $this->responseError($msgError, 'TOO_MANY_ATTEMPTS', 429);
            }
        });

        /* 500 message */
        $this->renderable(function (QueryException $e, Request $request) {
            if ($request->expectsJson()) {
                $msgError = ['message' => 'Internal Server Error'];
                return $this->responseError($msgError, 'INTERNAL_SERVER_ERROR', 500);
            }
        });
    }
}
