<?php

namespace App\Http\Controllers\Api;

use App\Enums\MessageCodeEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\LogSendEmail\LogSendEmailCollection;
use App\Services\Api\LogSendMailService;

class LogSendEmailController extends Controller
{
    public function __construct(LogSendMailService $service)
    {
        $this->service = $service;
    }

    public function list(Request $request, int $campaignId)
    {
        $this->service->attributes = $request->all();
        $this->service->attributes['filters']['campaign_id'] = $campaignId;

        if (!empty($list = $this->service->getList())) {

            return $this->responseSuccess(new LogSendEmailCollection($list));

        } else {
            return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
        }
    }
}
