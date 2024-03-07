<?php
namespace App\Http\Controllers\Api;

use App\Enums\MessageCodeEnum;
use App\Services\Api\UserService;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\SelfResource;
use App\Http\Resources\User\UserResource;
use App\Http\Requests\Api\User\ListRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Requests\Api\User\StoreRequest;

class UserController extends Controller
{
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function list(ListRequest $request)
    {
        $this->service->attributes = $request->all();

        if (!empty($list = $this->service->getList())) {
            return $this->responseSuccess(new UserCollection($list), trans('_response.success.index'));
        } else {
            return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
        }
    }

    public function self()
    {
        return $this->responseSuccess(SelfResource::make(auth()->user()), null);
    }

    public function detail(int $id)
    {
        if ($model = $this->service->getDetail($id)) {
            return $this->responseSuccess(UserResource::make($model), trans('_response.success.detail'));
        } else {
            return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
        }
    }


    public function store(StoreRequest $request)
    {
        $this->service->attributes = $request->all();

        if ($model = $this->service->store()) {
            return $this->responseSuccess(new UserResource($model), trans('_response.success.store'));
        } else {
            return $this->responseError('', MessageCodeEnum::FAILED_TO_STORE);
        }
    }
}
