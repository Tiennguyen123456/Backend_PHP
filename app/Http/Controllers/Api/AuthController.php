<?php
namespace App\Http\Controllers\Api;

use App\Enums\MessageCodeEnum;
use App\Events\UserCreatedEvent;
use App\Services\Api\AuthService;
use App\Services\Api\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Auth\LoginResource;
use App\Services\Api\PasswordResetService;
use App\Http\Requests\Api\User\LoginRequest;
use App\Http\Requests\Api\User\ResetPasswordRequest;
use App\Http\Requests\Api\User\SendMailResetPasswordRequest;

class AuthController extends Controller
{
    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function login(LoginRequest $request)
    {
        try {
            $this->service->attributes = $request->all();
            $result = $this->service->authenticate();

            if ($result['auth']) {
                return $this->responseSuccess(LoginResource::make(auth('api')->user()), $result['msg'], 200, MessageCodeEnum::LOGIN_SUCCESS);
            } else {
                return $this->responseError($result['msg'], MessageCodeEnum::USER_NAME_OR_PASSWORD_INCORRECT, 401);
            }
        } catch (\Throwable $th) {
            logger(' Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function sendMailResetPassword(SendMailResetPasswordRequest $request)
    {
        try {
            $userService = app(UserService::class);

            $user = $userService->findByEmail($request->email);
            if (!$user) {
                return $this->responseError('', MessageCodeEnum::EMAIL_NOT_FOUND);
            }

            event(new UserCreatedEvent($user));

            return $this->responseSuccess('', MessageCodeEnum::SUCCESS);
        } catch (\Throwable $th) {
            logger(' Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function resetPassword(ResetPasswordRequest $request, $token)
    {
        try {
            $userService          = app(UserService::class);
            $passwordResetService = app(PasswordResetService::class);

            $passwordReset = $passwordResetService->findByToken($token);
            if (empty($passwordReset)) {
                return $this->responseError('Token not found', MessageCodeEnum::TOKEN_NOT_FOUND);
            }
            $passwordResetService->deleteByEmail($passwordReset->email);

            $user = $userService->findByEmail($passwordReset->email);
            if (!$user) {
                return $this->responseError('', MessageCodeEnum::USER_NOT_FOUND);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            return $this->responseSuccess('', MessageCodeEnum::SUCCESS);
        } catch (\Throwable $th) {
            logger(' Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }
}
