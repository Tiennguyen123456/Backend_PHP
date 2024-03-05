<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Enums\MessageCodeEnum;
use App\Services\Api\EventService;
use App\Http\Controllers\Controller;
use App\Services\Api\CampaignService;
use App\Http\Resources\Company\CompanyResource;
use App\Http\Requests\Api\Campaign\StoreRequest;
use App\Http\Requests\Api\Campaign\UpdateRequest;
use App\Http\Resources\Campaign\CampaignCollection;
use App\Http\Resources\Campaign\CampaignResource;

class CampaignController extends Controller
{
    protected $eventService;

    public function __construct(CampaignService $service, EventService $eventService)
    {
        $this->service = $service;
        $this->eventService = $eventService;
    }

    public function list(Request $request)
    {
        try {
            $this->service->attributes = $request->all();

            if (!empty($list = $this->service->getList())) {
                return $this->responseSuccess(new CampaignCollection($list));
            } else {
                return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
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

    public function detail($id)
    {
        $model = $this->service->find($id);

        if (!empty($model)) {
            return $this->responseSuccess(new CampaignResource($model));
        } else {
            return $this->responseError('', MessageCodeEnum::RESOURCE_NOT_FOUND);
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

    public function handleAction(UpdateRequest $request)
    {
        try {
            $this->service->attributes = $request->all();

            if ($result = $this->service->handleAction()) {
                if (is_array($result) && $result['success'] === false) {
                    return $this->responseError('', $result['message']);
                }

                return $this->responseSuccess();
            } else {
                return $this->responseError('', MessageCodeEnum::FAILED_ACTION);
            }
        } catch (\Throwable $th) {
            logger(' Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return $this->responseError(trans('_response.failed.500'), MessageCodeEnum::INTERNAL_SERVER_ERROR, 500);
        }
    }
}
