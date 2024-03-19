<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use App\Enums\MessageCodeEnum;
use App\Services\Api\ClientService;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Requests\Api\Client\StoreRequest;
use App\Http\Requests\Api\Client\ImportRequest;
use App\Http\Resources\Client\ClientCollection;

class ClientController extends Controller
{
    public function __construct(ClientService $service)
    {
        $this->service = $service;
    }

    public function list(Request $request, int $eventId)
    {
        $this->service->attributes = $request->all();
        $this->service->attributes['filters']['event_id'] = $eventId;

        if (!empty($list = $this->service->getList())) {
            $summary = $this->service->summary();
            $totalClient = $summary['totalClient'];
            $totalClientCheckin = $summary['totalCheckin'];

            return $this->responseSuccess(new ClientCollection($list, $totalClient, $totalClientCheckin));
        } else {
            return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
        }
    }

    public function store(StoreRequest $request, int $eventId)
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

    public function import(ImportRequest $request, int $eventId)
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

    public function deleteClient(int $eventId, int $clientId)
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

    public function checkin(int $eventId, int $clientId)
    {
        try {
            $client = $this->service->find($clientId);

            if (!$client || $client->event_id !== $eventId) {
                return $this->responseError(trans('_response.failed.resource_not_found'), MessageCodeEnum::RESOURCE_NOT_FOUND);
            }

            if (!$client->isCheckin()) {
                $this->service->attributes['id'] = $clientId;
                $this->service->attributes['is_checkin'] = true;

                if (!$this->service->checkin()) {
                    return $this->responseError('', MessageCodeEnum::FAILED_TO_UPDATE);
                }
            }

            return $this->responseSuccess();
        } catch (\Throwable $th) {
            logger()->error(__METHOD__ . PHP_EOL . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function summary(Request $request, int $eventId)
    {
        try {
            $this->service->attributes = $request->all();
            $this->service->attributes['filters']['event_id'] = $eventId;

            $result = $this->service->summary();

            return $this->responseSuccess($result);
        } catch (\Throwable $th) {
            logger()->error(__METHOD__ . PHP_EOL . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function sample()
    {
        try {
            $filePath = public_path('storage/samples/Sample_Import_Client.xlsx');

            return response()->download($filePath, 'Sample_Import_Client.xlsx');
        } catch (\Throwable $th) {
            logger()->error(__METHOD__ . PHP_EOL . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function generateQrCode(Request $request)
    {
        try {
            $clientId = $request->get('client_id');
            $client = $this->service->find($clientId);

            if (!$client) {
                return $this->responseError(MessageCodeEnum::RESOURCE_NOT_FOUND);
            }
            $qrData = $this->service->encodeQrData($client->toArray());

            return QrCode::generate($qrData);
        } catch (\Throwable $th) {
            logger()->error(__METHOD__ . PHP_EOL . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function getQrData(int $eventId, int $clientId)
    {
        try {
            $client = $this->service->find($clientId);
            if (!$client || $client->event_id !== $eventId) {
                return $this->responseError(MessageCodeEnum::RESOURCE_NOT_FOUND);
            }
            $encryptData = $this->service->encodeQrData($client->toArray());

            return $this->responseSuccess($encryptData);
        } catch (\Throwable $th) {
            logger()->error(__METHOD__ . PHP_EOL . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }
}
