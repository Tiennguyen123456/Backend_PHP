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
        try {
            $this->service->attributes = $request->all();

            if (!empty($list = $this->service->getList())) {
                return $this->responseSuccess(new DefaultCollection($list));
            } else {
                return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::RESOURCE_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            logger(' Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function detail(int $id)
    {
        try {
            $model = $this->service->find($id);

            if (!empty($model)) {
                return $this->responseSuccess(new BaseResource($model));
            } else {
                return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            logger(' Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function remove(int $id)
    {
        try {
            if ($this->service->remove($id)) {
                return $this->responseSuccess(trans('_response.success.remove'), MessageCodeEnum::SUCCESS);
            } else {
                return $this->responseError('', MessageCodeEnum::FAILED_TO_REMOVE);
            }
        } catch (\Throwable $th) {
            logger(' Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function delete(int $id)
    {
        try {
            if ($this->service->delete($id)) {
                return $this->responseSuccess(trans('_response.success.delete'), MessageCodeEnum::SUCCESS);
            } else {
                return $this->responseError('', MessageCodeEnum::FAILED_TO_DELETE);
            }
        } catch (\Throwable $th) {
            logger(' Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }
}
