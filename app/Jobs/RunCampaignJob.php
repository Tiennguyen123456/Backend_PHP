<?php

namespace App\Jobs;

use App\Mail\MailCampaign;
use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use App\Services\Api\EventService;
use App\Services\Api\ClientService;
use App\Services\Api\CampaignService;
use Illuminate\Queue\SerializesModels;
use App\Services\Api\LogSendMailService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\Redis;

class RunCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $id;

    protected $clientService;

    protected $campaignService;

    protected $eventService;

    protected $logSendMailService;

    /**
     * Create a new job instance.
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->clientService = new ClientService();
        $this->campaignService = new CampaignService();
        $this->eventService = new EventService();
        $this->logSendMailService = new LogSendMailService();

        $this->redisCampaignClients = sprintf(config('redis.campaign.clients'), $id);
        $this->redisCampaignMailSent = sprintf(config('redis.campaign.mail_sents'), $id);
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
        if ($campaign->status != $campaign::STATUS_RUNNING) {
            logger('CAMPAIGN_IS_NOT_RUNNING');
            return;
        }

        // Email send
        $this->handleHistoryEmail($campaignId);

        // Load client
        $this->getClient($campaignId, $campaign->event_id);

        // Event data
        $eventVariables = $this->eventService->generateVariables($campaign->event_id);

        do {
            $client = Redis::rpop($this->redisCampaignClients);

            if (!blank($client)) {
                $clientVariables = $this->clientService->generateVariables(json_decode($client, true));

                $arVariables = array_merge($eventVariables, $clientVariables);

                // Replace variable on mail content
                $mailContent = $this->campaignService->replaceVariables($campaign->mail_content, $arVariables);

                // Send mail
                $mailData = [
                    'subject' => $campaign->mail_subject,
                    'content' => $mailContent,
                ];
                Mail::to($clientVariables['CLIENT_EMAIL'])->send(new MailCampaign($mailData));

                // Save log
                $this->logSendMailService->attributes = [
                    'campaign_id' => $campaign->id,
                    'client_id' => $client->id,
                    'email' => $client->email,
                    'subject' => $campaign->mail_subject,
                    'content' => $mailContent,
                    'status' => 'success',
                    'error' => '',
                    'sent_at' => now(),
                ];
                $this->logSendMailService->store();

                // Sleep
            }
        } while ( !blank($client) );
    }

    private function handleHistoryEmail($campaignId) : void
    {
        $page = 1;

        $this->logSendMailService->attributes['filters']['campaign_id'] = $campaignId;
        $this->logSendMailService->attributes['orderBy'] = 'id';
        $this->logSendMailService->attributes['orderDesc'] = false;

        do {
            $this->logSendMailService->attributes['page'] = $page++;

            $result = $this->logSendMailService->getList();

            foreach ($result as $log) {
                Redis::sadd($this->redisCampaignMailSent, $log->email);
            }
        } while ( !blank($result) );
    }

    private function getClient($eventId)
    {
        try {
            $page = 1;
            $count = 0;

            $this->service->attributes['orderBy'] = 'id';
            $this->service->attributes['orderDesc'] = false;

            do {
                $this->service->attributes['page'] = $page++;

                $result = $this->service->getClientsByEventId($eventId);

                foreach ($result as $client) {
                    $email = $client->email;

                    if (!empty($email) && !Redis::sismember($this->redisCampaignMailSent, $email)) {
                        $count++;
                        Redis::lpush($this->redisCampaignClients, json_encode($client));
                    }
                }
            } while ( !blank($result) );
        } catch (\Throwable $th) {
            logger('Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
        }
    }
}
