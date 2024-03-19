<?php
namespace App\Services\Api;

use App\Jobs\RunCampaignJob;
use App\Services\BaseService;
use App\Enums\MessageCodeEnum;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Redis;
use App\Repositories\Campaign\CampaignRepository;

class CampaignService extends BaseService
{
    public function __construct()
    {
        $this->repo = new CampaignRepository();
    }

    public function store()
    {
        $attrs = [
            'name'              => $this->attributes['name'],
            'run_time'          => $this->attributes['run_time'] ?? null,
            'status'            => $this->attributes['status'],
            'mail_subject'      => $this->attributes['mail_subject'],
            'sender_email'      => $this->attributes['sender_email'] ?? null,
            'sender_name'       => $this->attributes['sender_name'] ?? null,
            'description'       => $this->attributes['description'] ?? null,
            'filter_client'     => serialize($this->attributes['filter_client']),
        ];

        if (!isset($this->attributes['id'])) {
            $attrMores = [
                'company_id'        => $this->attributes['company_id'],
                'event_id'          => $this->attributes['event_id'],
                'mail_content'      => $this->attributes['mail_content'],
                'created_by'        => auth()->user()->id,
                'updated_by'        => auth()->user()->id
            ];
        } else {
            $attrMores = [
                'id'            => $this->attributes['id'],
                'updated_by'    => auth()->user()->id,
            ];
        }

        return $this->storeAs($attrs, $attrMores);
    }

    public function updateMailContent()
    {
        $id = $this->attributes['id'];

        $model = $this->repo->find($id);

        if (!empty($model)) {
            $model->update($this->attributes);
            return $model;
        }
    }

    public function handleAction()
    {
        $id = $this->attributes['id'];
        $action = $this->attributes['action'];

        $model = $this->repo->find($id);

        if (empty($model)) return false;

        switch ($action) {
            case 'START':
                return $this->runCampaign($model);

            case 'PAUSE':
                return $this->pauseCampaign($model);

            case 'STOP':
                return $this->stopCampaign($model);
        }
    }

    private function runCampaign($model)
    {
        if ($model->status != $model::STATUS_NEW && $model->status != $model::STATUS_PAUSED) {
            return [
                'success' => false,
                'message' => MessageCodeEnum::CAMPAIGN_IS_NOT_NEW_OR_PAUSED,
            ];
        }

        // Check if Campaign has no client
        $clientService = new ClientService();
        $clientService->attributes['filters'] = unserialize($model->filter_client);
        $clientService->attributes['filters']['event_id'] = $model->event_id;

        if ($clientService->count() == 0) {
            return [
                'success' => false,
                'message' => MessageCodeEnum::CAMPAIGN_HAS_NO_CLIENT,
            ];
        }

        $model->update([
            'status' => $model::STATUS_RUNNING
        ]);

        // Call Job Run Campaign
        RunCampaignJob::dispatch($model->id, auth()->id());

        return ['success' => true];
    }

    private function pauseCampaign($model)
    {
        if ($model->status != $model::STATUS_RUNNING) {
            return [
                'success' => false,
                'message' => MessageCodeEnum::CAMPAIGN_IS_NOT_RUNNING,
            ];
        }

        $model->update([
            'status' => $model::STATUS_PAUSED
        ]);

        Redis::del(sprintf(config('redis.campaign.status'), $model->id));

        return ['success' => true];
    }

    private function stopCampaign($model)
    {
        if ($model->status == $model::STATUS_STOPPED) {
            return [
                'success' => false,
                'message' => MessageCodeEnum::CAMPAIGN_IS_ALREADY_STOPPED,
            ];
        }

        $model->update([
            'status' => $model::STATUS_STOPPED
        ]);

        Redis::del(sprintf(config('redis.campaign.status'), $model->id));

        return ['success' => true];
    }

    public function replaceVariables(string $content, array $variables)
    {
        foreach ($variables as $key => $value) {
            if ($key === 'CLIENT_QR_CODE') {
                $value = "<img src='" . config('app.campaign.cid_qr_code_image') . "' alt='QR Image'>";
            }
            $content = str_replace('{{'.$key.'}}', $value, $content);
        }

        return $content;
    }
}
