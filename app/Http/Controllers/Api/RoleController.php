<?php
namespace App\Http\Controllers\Api;

use App\Services\Api\RoleService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Role\AssignRequest;
use App\Http\Requests\Api\Role\StoreRequest;
use App\Http\Resources\Role\RoleResource;

class RoleController extends Controller
{
    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    public function store(StoreRequest $request)
    {
        $this->service->attributes = $request->all();

        if ($model = $this->service->store()) {
            return $this->responseSuccess(new RoleResource($model), trans('_response.success.store'));
        } else {
            return $this->responseError('', 'FAILED_TO_STORE');
        }
    }

    public function assign(AssignRequest $request)
    {
        $this->service->attributes = $request->all();

        if ($this->service->assign()) {
            return $this->responseSuccess(null, trans('_response.success.assign'));
        } else {
            return $this->responseError('', 'RESOURCE_NOT_FOUND');
        }
    }
}
