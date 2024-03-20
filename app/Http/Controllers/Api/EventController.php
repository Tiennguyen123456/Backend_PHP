<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Enums\MessageCodeEnum;
use App\Services\Api\EventService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Event\QrCheckinRequest;
use App\Http\Requests\Api\Event\ReportRequest;
use App\Http\Resources\DefaultCollection;
use App\Http\Resources\Event\EventResource;
use App\Http\Requests\Api\Event\StoreRequest;
use App\Http\Resources\Event\EventCollection;
use App\Services\Api\EventCustomFieldService;
use App\Http\Requests\Api\Event\StoreCustomFieldRequest;
use App\Services\Api\ClientService;

class EventController extends Controller
{
    protected $clientService;

    public function __construct(EventService $service, ClientService $clientService)
    {
        $this->service = $service;
        $this->clientService = $clientService;
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
            logger('Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
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
           logger('Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
           return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function detail(int $id)
    {
        try {
            $model = $this->service->find($id);

            if (!empty($model)) {
                return $this->responseSuccess(new EventResource($model));
            } else {
                return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            logger('Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
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
            logger('Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function listCustomField(Request $request, int $eventId)
    {
        try {
            $service = app(EventCustomFieldService::class);
            $service->attributes = $request->all();
            $service->attributes['filters']['event_id'] = $eventId;

            if (!empty($list = $service->getList())) {
                return $this->responseSuccess(new DefaultCollection($list), trans('_response.success.index'));
            } else {
                return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::RESOURCE_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            logger('Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function storeCustomField(StoreCustomFieldRequest $request, int $eventId)
    {
        try {
            $service = app(EventCustomFieldService::class);
            $service->attributes = $request->all();
            $service->attributes['event_id'] = $eventId;

            if ($service->store()) {
                return $this->responseSuccess();
            } else {
                return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::RESOURCE_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            logger('Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function removeCustomField(int $id)
    {
        try {
            $service = app(EventCustomFieldService::class);

            if ($service->delete($id)) {
                return $this->responseSuccess(null, trans('_response.success.remove'));
            } else {
                return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::FAILED_TO_REMOVE);
            }
        } catch (\Throwable $th) {
            logger('Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function listMainField()
    {
        try {
            if ($data = $this->service->listMainField()) {
                return $this->responseSuccess($data);
            } else {
                return $this->responseError();
            }
        } catch (\Throwable $th) {
            logger('Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function qrCheckin(QrCheckinRequest $request)
    {
        try {
            $qrData = $this->clientService->decodeQrData($request->get('code'));

            if (!$qrData) {
                return $this->responseError('', MessageCodeEnum::QR_CODE_INVALID);
            }

            $client = $this->clientService->find($qrData['id']);
            if (!$client) {
                return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
            }

            if (!$client->is_checkin) {
                $client->is_checkin = true;
                $client->checkin_at = now();
                $client->save();
            } else {
                return $this->responseSuccess('', MessageCodeEnum::CLIENT_HAVE_ALDREADY_CHECKIN);
            }

            return $this->responseSuccess();
        } catch (\Throwable $th) {
            logger('Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function dashboardReport(ReportRequest $request)
    {
        try {
            $this->service->attributes = $request->all();
            $data = $this->service->getDashboardReport();
            return $this->responseSuccess($data);
        } catch (\Throwable $th) {
            logger('Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }
}
