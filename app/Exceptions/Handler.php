<?php

namespace App\Exceptions;

use App\Enums\MessageCodeEnum;
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
                return $this->responseError($msgError, MessageCodeEnum::METHOD_IS_NOT_SUPPORT, 405);
            }
        });

        /* 401 message */
        $this->renderable(function (AuthenticationException $e, Request $request) {
            /* This is authorized by logged in users */
            if ($request->expectsJson()) {
                $msgError = ['message' => 'This action is unauthorized'];
                return $this->responseError($msgError, MessageCodeEnum::UNAUTHORIZED_ACTION, 401);
            }
        });

        /* 403 message */
        $this->renderable(function (UnauthorizedException $e, Request $request) {
            /* This is authorized by roles or permisions assigned to users */
            if ($request->expectsJson()) {
                $msgError = ['message' => 'This action is unauthorized'];
                return $this->responseError($msgError, MessageCodeEnum::PERMISSION_DENIED, 403);
            }
        });

        /* 403 message */
        $this->renderable(function (AccessDeniedHttpException $e, Request $request) {
            /* This is authorized by request class */
            if ($request->expectsJson()) {
                $msgError = ['message' => 'Access Denied'];
                return $this->responseError($msgError, MessageCodeEnum::ACCESS_DENIED, 403);
            }
        });

        /* 400 message */
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                $msgError = ['message' => 'Page Not Found'];
                return $this->responseError($msgError, MessageCodeEnum::PAGE_NOT_FOUND);
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
                return $this->responseError($msgError, MessageCodeEnum::TOO_MANY_ATTEMPTS, 429);
            }
        });

        /* 500 message */
        $this->renderable(function (QueryException $e, Request $request) {
            if ($request->expectsJson()) {
                $msgError = ['message' => 'Internal Server Error'];
                return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
            }
        });
    }
}
