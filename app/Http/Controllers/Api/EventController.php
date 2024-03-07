<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Enums\MessageCodeEnum;
use App\Services\Api\EventService;
use App\Http\Controllers\Controller;
use App\Http\Resources\DefaultCollection;
use App\Http\Resources\Event\EventResource;
use App\Http\Requests\Api\Event\StoreRequest;
use App\Http\Resources\Event\EventCollection;
use App\Services\Api\EventCustomFieldService;
use App\Http\Requests\Api\Event\AssignCompanyRequest;
use App\Http\Requests\Api\Event\StoreCustomFieldRequest;

class EventController extends Controller
{
    public function __construct(EventService $service)
    {
        $this->service = $service;
    }

    public function list(Request $request)
    {
        try {
            $this->service->attributes = $request->all();

            if (!empty($list = $this->service->getList())) {
                return $this->responseSuccess(new EventCollection($list), trans('_response.success.index'));
            } else {
                return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            logger(' Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $this->service->attributes = $request->all();

            if ($model = $this->service->store()) {
                return $this->responseSuccess(new EventResource($model), trans('_response.success.store'));
            } else {
                return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::FAILED_TO_STORE);
            }
        } catch (\Throwable $th) {
           logger(' Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

           return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function detail(int $id)
    {
        $model = $this->service->find($id);

        if (!empty($model)) {
            return $this->responseSuccess(new EventResource($model));
        } else {
            return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
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


    public function assignCompany(AssignCompanyRequest $request)
    {
        $this->service->attributes = $request->all();

        if ($this->service->assignCompany()) {
            return $this->responseSuccess(null, trans('_response.success.assign'));
        } else {
            return $this->responseError(trans('_response.failed.400'), 400);
        }
    }

    public function listCustomField(Request $request, int $eventId)
    {
        $eventCustomFieldService = app(EventCustomFieldService::class);
        $eventCustomFieldService->attributes = $request->all();
        $eventCustomFieldService->attributes['filters']['event_id'] = $eventId;

        if (!empty($list = $eventCustomFieldService->getList())) {
            return $this->responseSuccess(new DefaultCollection($list), trans('_response.success.index'));
        } else {
            return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::RESOURCE_NOT_FOUND);
        }
    }

    public function storeCustomField(StoreCustomFieldRequest $request, int $eventId)
    {
        $eventCustomFieldService = app(EventCustomFieldService::class);

        try {
            $eventCustomFieldService->attributes['data'] = $request->all();
            $eventCustomFieldService->attributes['event_id'] = $eventId;

            $eventCustomFieldService->store();

            $eventCustomFieldService->attributes['filters']['event_id'] = $eventId;
            $list = $eventCustomFieldService->getList();

            return $this->responseSuccess(new DefaultCollection($list), trans('_response.success.index'));
        } catch (\Throwable $th) {
            return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::FAILED_TO_STORE);
        }
    }

    public function removeCustomField(int $id)
    {
        $eventCustomFieldService = app(EventCustomFieldService::class);

        if ($eventCustomFieldService->remove($id)) {
            return $this->responseSuccess(null, trans('_response.success.remove'));
        } else {
            return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::FAILED_TO_REMOVE);
        }
    }
}
