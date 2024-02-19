<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Resources\DefaultCollection;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, ApiResponser;

    public $service;
    public $cacher;

    public function index(Request $request)
    {
        $this->service->attributes = $request->all();

        if (!empty($list = $this->service->getList())) {
            return $this->responseSuccess(new DefaultCollection($list), trans('_response.success.index'));
        } else {
            return $this->responseError(trans('_response.failed.400'), 'RESOURCE_NOT_FOUND');
        }
    }

    public function detail($id)
    {
        $model = $this->service->find($id);

        if (!empty($model)) {
            return $this->responseSuccess(new BaseResource($model), trans('_response.success.detail'));
        } else {
            return $this->responseError('', 'RESOURCE_NOT_FOUND');
        }
    }

    public function remove($id)
    {
        if ($this->service->remove($id)) {
            return $this->responseSuccess(null, trans('_response.success.remove'));
        } else {
            return $this->responseError('', 'UNABLE_TO_REMOVE_ITEM');
        }
    }

    public function delete($id)
    {
        if ($this->service->delete($id)) {
            return $this->responseSuccess(null, trans('_response.success.delete'));
        } else {
            return $this->responseError('', 'UNABLE_TO_DELETE_ITEM');
        }
    }
}
