<?php

namespace App\Http\Controllers\Api;

use App\Enums\MessageCodeEnum;
use App\Services\Api\PostService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Post\PostResource;
use App\Http\Requests\Api\Post\StoreRequest;

class PostController extends Controller
{
    public function __construct(PostService $service)
    {
        $this->service = $service;
    }

    public function store(StoreRequest $request)
    {
        try {
            $this->service->attributes = $request->all();

            if ($model = $this->service->store()) {
                return $this->responseSuccess(new PostResource($model), trans('_response.success.store'));
            } else {
                return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::FAILED_TO_STORE);
            }
        } catch (\Throwable $th) {
           logger('Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
           return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }
}
