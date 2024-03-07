<?php
namespace App\Http\Controllers\Api;

use App\Enums\MessageCodeEnum;
use App\Services\Api\PermissionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Permission\AssignToRoleRequest;
use App\Http\Resources\Permission\PermissionCollection;

class PermissionController extends Controller
{
    public function __construct(PermissionService $service)
    {
        $this->service = $service;
    }

    public function getListFromCurrentUser()
    {
        $permissions = $this->service->getListFromCurrentUser();

        if (is_array($permissions) && count($permissions)) {
            return $this->responseSuccess(
                new PermissionCollection($permissions),
                trans('_response.success.index')
            );
        } else {
            return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
        }
    }

    public function getListFromRole(int $roleId)
    {
        $permissions = $this->service->getListFromRole($roleId);

        if ($permissions) {
            return $this->responseSuccess(
                new PermissionCollection($permissions),
                trans('_response.success.index')
            );
        } else {
            return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
        }
    }

    public function assignToRole(AssignToRoleRequest $request)
    {
        $this->service->attributes = $request->all();

        if ($this->service->assignToRole()) {
            return $this->responseSuccess(null, trans('_response.success.assign'));
        } else {
            return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
        }
    }
}
