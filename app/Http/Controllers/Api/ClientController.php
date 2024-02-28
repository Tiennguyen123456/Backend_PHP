<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use App\Enums\MessageCodeEnum;
use App\Services\Api\ClientService;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Http\Requests\Api\Client\StoreRequest;
use App\Http\Requests\Api\Client\ImportRequest;
use App\Http\Resources\Client\ClientCollection;

class ClientController extends Controller
{
    public function __construct(ClientService $service)
    {
        $this->service = $service;
    }

    public function list(Request $request, $eventId)
    {
        $this->service->attributes = $request->all();

        if (!empty($list = $this->service->getList())) {
            $this->service->attributes['filters']['event_id'] = $eventId;
            $totalClient = $this->service->count();

            $this->service->attributes['filters']['is_checkin'] = true;
            $totalClientCheckin = $this->service->count();

            return $this->responseSuccess(new ClientCollection($list, $totalClient, $totalClientCheckin));
        } else {
            return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
        }
    }

    public function store(StoreRequest $request, $eventId)
    {
        try {
            $this->service->attributes = $request->all();
            $this->service->attributes['event_id'] = $eventId;

            if ($model = $this->service->store()) {
                return $this->responseSuccess(new BaseResource($model), trans('_response.success.store'));
            } else {
                return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::FAILED_TO_STORE);
            }
        } catch (\Throwable $th) {
            logger()->error(__METHOD__ . ' -> ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function import(ImportRequest $request, $eventId)
    {
        try {
            $filePath = FileHelper::storeFile(auth()->user()->id, $request->file('file'));

            if (blank($filePath))
                return $this->responseError('', MessageCodeEnum::FILE_UPLOAD_FAILED);

            $this->service->attributes['event_id'] = $eventId;
            $this->service->attributes['filePath'] = $filePath;

            $result = $this->service->import();

            if ($result['status'] === 'success') {
                return $this->responseSuccess();
            } else {
                return $this->responseError('', $result['message']);
            }
        } catch (\Throwable $th) {
            logger()->error(__METHOD__ . ' -> ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function deleteClient($eventId, $clientId)
    {
        try {
            $client = $this->service->find($clientId);

            if (!$client || $client->event_id !== $eventId) {
                return $this->responseError(trans('_response.failed.resource_not_found'), MessageCodeEnum::RESOURCE_NOT_FOUND);
            }

            if ($this->service->delete($clientId)) {
                return $this->responseSuccess();
            } else {
                return $this->responseError('', MessageCodeEnum::FAILED_TO_DELETE);
            }
        } catch (\Throwable $th) {
            logger()->error(__METHOD__ . PHP_EOL . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function checkin($eventId, $clientId)
    {
        try {
            $client = $this->service->find($clientId);

            if (!$client || $client->event_id !== $eventId) {
                return $this->responseError(trans('_response.failed.resource_not_found'), MessageCodeEnum::RESOURCE_NOT_FOUND);
            }

            if (!$client->isCheckin()) {
                $this->service->attributes['id'] = $clientId;
                $this->service->attributes['is_checkin'] = true;

                if (!$this->service->store()) {
                    return $this->responseError('', MessageCodeEnum::FAILED_TO_UPDATE);
                }
            }

            return $this->responseSuccess();
        } catch (\Throwable $th) {
            logger()->error(__METHOD__ . PHP_EOL . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function summary($eventId)
    {
        try {
            $this->service->attributes['event_id'] = $eventId;
            $result = $this->service->summary();

            return $this->responseSuccess($result);
        } catch (\Throwable $th) {
            logger()->error(__METHOD__ . PHP_EOL . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }
}
