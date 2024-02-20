<?php

namespace App\Http\Controllers\Api;

use App\Enums\MessageCodeEnum;
use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\DefaultCollection;
use App\Http\Requests\Api\Client\ImportRequest;
use App\Services\Api\ClientService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(ClientService $service)
    {
        $this->service = $service;
    }

    public function list(Request $request, $eventId)
    {
        $this->service->attributes = $request->all();
        $this->service->attributes['filters']['event_id'] = $eventId;

        if (!empty($list = $this->service->getList())) {
            return $this->responseSuccess(new DefaultCollection($list));
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
            return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::INTERNAL_SERVER_ERROR);
        }
    }
}
