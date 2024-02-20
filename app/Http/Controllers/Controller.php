<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Enums\MessageCodeEnum;
use App\Http\Resources\BaseResource;
use App\Http\Resources\DefaultCollection;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, ApiResponser;

    public $service;
    public $cacher;

    public function index(Request $request)
    {
        $this->service->attributes = $request->all();

        if (!empty($list = $this->service->getList())) {
            return $this->responseSuccess(new DefaultCollection($list));
        } else {
            return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::RESOURCE_NOT_FOUND);
        }
    }

    public function detail($id)
    {
        $model = $this->service->find($id);

        if (!empty($model)) {
            return $this->responseSuccess(new BaseResource($model));
        } else {
            return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
        }
    }

    public function remove($id)
    {
        if ($this->service->remove($id)) {
            return $this->responseSuccess(null, trans('_response.success.remove'));
        } else {
            return $this->responseError('', MessageCodeEnum::FAILED_TO_REMOVE);
        }
    }

    public function delete($id)
    {
        if ($this->service->delete($id)) {
            return $this->responseSuccess(null, trans('_response.success.delete'));
        } else {
            return $this->responseError('', MessageCodeEnum::FAILED_TO_DELETE);
        }
    }
}
