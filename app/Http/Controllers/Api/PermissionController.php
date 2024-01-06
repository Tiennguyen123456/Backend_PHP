<?php
namespace App\Http\Controllers\Api;

use App\Services\Api\PermissionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Permission\AssignToRoleRequest;
use App\Http\Requests\Api\Permission\RevokeFromRoleRequest;
use GuzzleHttp\Psr7\Request;

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
                $permissions,
                trans('_response.success.index')
            );
        } else {
            return $this->responseError([
                'message' => trans('_response.failed.400')
            ], 400);
        }
    }

    public function getListFromRole($roleId)
    {
        $permissions = $this->service->getListFromRole($roleId);

        if (is_array($permissions) && count($permissions)) {
            return $this->responseSuccess(
                $permissions,
                trans('_response.success.index')
            );
        } else {
            return $this->responseError([
                'message' => trans('_response.failed.400')
            ], 400);
        }
    }

    public function assignToRole(AssignToRoleRequest $request)
    {
        $this->service->attributes = $request->all();

        if ($this->service->assignToRole()) {
            return $this->responseSuccess(null, trans('_response.success.assign'));
        } else {
            return $this->responseError([
                'message' => trans('_response.failed.400')
            ], 400);
        }
    }

    public function revokeFromRole($roleId, RevokeFromRoleRequest $request)
    {
        $this->service->attributes = $request->all();

        if ($this->service->revokeFromRole($roleId)) {
            return $this->responseSuccess(null, trans('_response.success.revoke'));
        } else {
            return $this->responseError([
                'message' => trans('_response.failed.400')
            ], 400);
        }
    }
}
