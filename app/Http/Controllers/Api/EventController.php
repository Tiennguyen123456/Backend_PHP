<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Enums\MessageCodeEnum;
use App\Services\Api\EventService;
use App\Http\Controllers\Controller;
use App\Http\Resources\DefaultCollection;
use App\Http\Requests\Api\Event\StoreRequest;
use App\Services\Api\EventCustomFieldService;
use App\Http\Requests\Api\Event\AssignCompanyRequest;
use App\Http\Requests\Api\Event\StoreCustomFieldRequest;
use App\Http\Resources\Event\EventResource;

class EventController extends Controller
{
    public function __construct(EventService $service)
    {
        $this->service = $service;
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

    public function assignCompany(AssignCompanyRequest $request)
    {
        $this->service->attributes = $request->all();

        if ($this->service->assignCompany()) {
            return $this->responseSuccess(null, trans('_response.success.assign'));
        } else {
            return $this->responseError(trans('_response.failed.400'), 400);
        }
    }

    public function listCustomField(Request $request, $eventId)
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

    public function storeCustomField(StoreCustomFieldRequest $request, $eventId)
    {
        $eventCustomFieldService = app(EventCustomFieldService::class);

        try {
            foreach ($request->all() as $array) {
                $eventCustomFieldService->updateOrCreate(
                    ['event_id' => $eventId, 'name' => $array['name']],
                    [
                        'event_id' => $eventId,
                        'name' => $array['name'],
                        'value' => $array['value'],
                        'created_by' => auth()->user()->id,
                        'updated_by' => auth()->user()->id,
                    ]
                );
            }

            $eventCustomFieldService->attributes['filters']['event_id'] = $eventId;
            $list = $eventCustomFieldService->getList();

            return $this->responseSuccess(new DefaultCollection($list), trans('_response.success.index'));
        } catch (\Throwable $th) {
            return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::FAILED_TO_STORE);
        }
    }

    public function removeCustomField($id)
    {
        $eventCustomFieldService = app(EventCustomFieldService::class);

        if ($eventCustomFieldService->remove($id)) {
            return $this->responseSuccess(null, trans('_response.success.remove'));
        } else {
            return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::FAILED_TO_REMOVE);
        }
    }
}
