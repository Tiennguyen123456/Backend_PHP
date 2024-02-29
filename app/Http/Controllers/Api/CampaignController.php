<?php
namespace App\Http\Controllers\Api;

use App\Enums\MessageCodeEnum;
use App\Services\Api\EventService;
use App\Http\Controllers\Controller;
use App\Services\Api\CampaignService;
use App\Http\Resources\Company\CompanyResource;
use App\Http\Requests\Api\Campaign\StoreRequest;

class CampaignController extends Controller
{
    protected $eventService;

    public function __construct(CampaignService $service, EventService $eventService)
    {
        $this->service = $service;
        $this->eventService = $eventService;
    }

    public function store(StoreRequest $request)
    {
        try {
            // Get email content fron eventId
            $eventId = $request->get('event_id');

            $event = $this->eventService->find($eventId);

            if (blank($event->email_content)) {
                return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::EMAIL_CONTENT_IS_EMPTY);
            }

            $this->service->attributes = $request->all();
            $this->service->attributes['mail_content'] = $event->email_content;
            $this->service->attributes['sender_email'] = $event->sender_email ?? 'system@gmail.com';
            $this->service->attributes['sender_name'] = $event->sender_name ?? 'System';

            if ($model = $this->service->store()) {
                return $this->responseSuccess(new CompanyResource($model));
            } else {
                return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::FAILED_TO_STORE);
            }
        } catch (\Throwable $th) {
            logger(' Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }

    public function updateMailContent($id)
    {
        try {
            $campaign = $this->service->find($id);
            if (blank($campaign)) {
                return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::CAMPAIGN_NOT_FOUND);
            }

            $event = $this->eventService->find($id);
            if (blank($event->email_content)) {
                return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::EMAIL_CONTENT_IS_EMPTY);
            }

            $this->service->attributes['id'] = $id;
            $this->service->attributes['mail_content'] = $event->email_content;

            if ($this->service->updateMailContent()) {
                return $this->responseSuccess();
            } else {
                return $this->responseError('', MessageCodeEnum::FAILED_TO_UPDATE);
            }
        } catch (\Throwable $th) {
            logger(' Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }
}
