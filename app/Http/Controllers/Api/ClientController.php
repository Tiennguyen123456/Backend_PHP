<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\DefaultCollection;
use App\Http\Requests\Api\Client\ImportRequest;
use App\Services\Api\EventService;
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
            return $this->responseSuccess(new DefaultCollection($list), trans('_response.success.index'));
        } else {
            return $this->responseError(trans('_response.failed.400'), 400);
        }
    }

    public function import(ImportRequest $request, $eventId)
    {
        try {
            $filePath = FileHelper::storeFile(auth()->user()->id, $request->file('file'));

            if (blank($filePath))
                return $this->responseError(trans('_response.failed.store_file'), 400);

            $this->service->attributes['event_id'] = $eventId;
            $this->service->attributes['filePath'] = $filePath;

            if ($this->service->import()) {
                return $this->responseSuccess('', trans('_response.success.index'));
            }
        } catch (\Throwable $th) {
            logger('Error: ' . __METHOD__ . ' -> ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
        }

        return $this->responseError(trans('_response.failed.400'), 400);
    }
}
