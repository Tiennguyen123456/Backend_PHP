<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Enums\MessageCodeEnum;
use App\Services\Api\RoleService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Role\RoleCollection;
use App\Http\Resources\Role\RoleResource;
use App\Http\Requests\Api\Role\StoreRequest;
use App\Http\Requests\Api\Role\AssignRequest;

class RoleController extends Controller
{
    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    public function list(Request $request)
    {
        $this->service->attributes = $request->all();

        if (!empty($list = $this->service->getList())) {
            return $this->responseSuccess(new RoleCollection($list), trans('_response.success.index'));
        } else {
            return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
        }
    }

    public function store(StoreRequest $request)
    {
        $this->service->attributes = $request->all();

        if ($model = $this->service->store()) {
            return $this->responseSuccess(new RoleResource($model), trans('_response.success.store'));
        } else {
            return $this->responseError('', MessageCodeEnum::FAILED_TO_STORE);
        }
    }

    public function assign(AssignRequest $request)
    {
        $this->service->attributes = $request->all();

        if ($this->service->assign()) {
            return $this->responseSuccess(null, trans('_response.success.assign'));
        } else {
            return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
        }
    }
}
