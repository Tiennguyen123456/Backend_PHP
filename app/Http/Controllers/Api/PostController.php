<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use App\Enums\MessageCodeEnum;
use App\Services\Api\PostService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Post\PostResource;
use App\Http\Resources\Post\PostCollection;
use App\Http\Requests\Api\Post\StoreRequest;

class PostController extends Controller
{
    public function __construct(PostService $service)
    {
        $this->service = $service;
    }

    public function list(Request $request)
    {
        try {
            $this->service->attributes = $request->all();

            if (!empty($list = $this->service->getList())) {
                return $this->responseSuccess(new PostCollection($list), MessageCodeEnum::SUCCESS);
            } else {
                return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            logger('Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $this->service->attributes = $request->all();

            // Handle file upload
            if ($request->file('background_img')) {
                $filePath = FileHelper::storeFile(auth()->user()->id, $request->file('background_img'));

                if (blank($filePath))
                    return $this->responseError('', MessageCodeEnum::FAILED_TO_STORE);

                $this->service->attributes['background_img'] = $filePath;
            }

            if ($model = $this->service->store()) {
                return $this->responseSuccess(new PostResource($model), MessageCodeEnum::SUCCESS);
            } else {
                return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::FAILED_TO_STORE);
            }
        } catch (\Throwable $th) {
           logger('Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
           return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function detail(int $id)
    {
        try {
            $model = $this->service->find($id);

            if (!empty($model)) {
                return $this->responseSuccess(new PostResource($model));
            } else {
                return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            logger('Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function deleteBackgroundImg(int $id)
    {
        try {
            $model = $this->service->find($id);

            if (blank($model))
                return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);

            if (blank($model->background_img))
                return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);

            if ($this->service->deleteBackgroundImg($model)) {
                return $this->responseSuccess('', MessageCodeEnum::SUCCESS);
            } else {
                return $this->responseError('', MessageCodeEnum::FAILED_TO_DELETE);
            }
        } catch (\Throwable $th) {
            logger('Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }
}
