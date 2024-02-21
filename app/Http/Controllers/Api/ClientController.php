<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use App\Enums\MessageCodeEnum;
use App\Services\Api\ClientService;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Api\Client\ImportRequest;
use App\Http\Requests\Api\Client\UpdateRequest;
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
            return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function update(UpdateRequest $request, $eventId, $clientId)
    {
        try {
            $arParam = [
                'id'        => $clientId,
                'event_id'  => $eventId,
            ];

            $validator = Validator::make($arParam, [
                'id'        => ['required', 'integer', Rule::exists('clients')->where('event_id', $eventId)],
                'event_id'  => ['required', 'integer', 'exists:events,id'],
            ]);

            if ($validator->fails()) {
                return $this->responseError($validator->errors(), MessageCodeEnum::VALIDATION_ERROR, 422);
            }

            $this->service->attributes = $request->all();
            $this->service->attributes['id'] = $clientId;

            if ($model = $this->service->store()) {
                return $this->responseSuccess(new BaseResource($model));
            } else {
                return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::FAILED_TO_UPDATE);
            }
        } catch (\Throwable $th) {
            logger()->error(__METHOD__ . PHP_EOL . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError('', MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function deleteClient($eventId, $clientId)
    {
        try {
            $arParam = [
                'id'        => $clientId,
                'event_id'  => $eventId,
            ];

            $validator = Validator::make($arParam, [
                'id'        => ['required', 'integer', Rule::exists('clients')->where('event_id', $eventId)],
                'event_id'  => ['required', 'integer', Rule::exists('events', 'id')],
            ]);

            if ($validator->fails()) {
                return $this->responseError($validator->errors(), MessageCodeEnum::VALIDATION_ERROR, 422);
            }

            if ($this->service->delete($clientId)) {
                $this->service->updateCache($eventId);

                return $this->responseSuccess();
            } else {
                return $this->responseError('', MessageCodeEnum::FAILED_TO_DELETE);
            }
        } catch (\Throwable $th) {
            logger()->error(__METHOD__ . PHP_EOL . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError('', MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }
}
