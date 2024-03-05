<?php

namespace App\Jobs;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use App\Services\Api\ClientService;
use App\Services\Api\CampaignService;
use App\Services\Api\EventService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class RunCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $id;

    protected $clientService;

    protected $campaignService;

    protected $eventService;

    /**
     * Create a new job instance.
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->clientService = new ClientService();
        $this->campaignService = new CampaignService();
        $this->eventService = new EventService();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger('run campaign');
        $campaignId = $this->id;

        $campaign = $this->campaignService->find($campaignId);

        if (empty($campaign)) {
            logger('Campaign not found');
            return;
        }
        if ($campaign->status != $campaign::STATUS_NEW && $campaign->status != $campaign::STATUS_PAUSED) {
            logger('CAMPAIGN_IS_NOT_NEW_OR_PAUSED');
            return;
        }

        // Get client list
        $this->clientService->attributes['filters'] = unserialize($campaign->filter_client);

        $clients = $this->clientService->getClientsByEventId($campaign->event_id);

        if (empty($clients)) {
            logger('No client found');
            $campaign->update(['status' => $campaign::STATUS_FINISHED]);
            return;
        }

        // Generate all variables
        $eventVariables = $this->eventService->generateVariables($campaign->event_id);

        $emailContent = $campaign->mail_content;

        // Send mail
        // foreach ($clients as $client) {
        //     $clientVariables = $this->clientService->getVariables($client->id);

        //     $variables = array_merge($eventVariables, $clientVariables);

        //     $mailContent = $this->campaignService->replaceVariables($emailContent, $variables);
        // }
    }
}
