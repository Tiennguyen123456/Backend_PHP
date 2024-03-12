<?php

namespace App\Jobs;

use App\Mail\MailCampaign;
use Illuminate\Bus\Queueable;
use App\Services\Api\EventService;
use App\Services\Api\ClientService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Services\Api\CampaignService;
use Illuminate\Support\Facades\Redis;
use Illuminate\Queue\SerializesModels;
use App\Services\Api\LogSendMailService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Throwable;

class RunCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $id;

    private $userId;

    protected $clientService;

    protected $campaignService;

    protected $eventService;

    protected $logSendMailService;

    protected $redisCampaignClients;

    protected $redisCampaignMailSent;

    protected $redisCampaignStatus;

    /**
     * Create a new job instance.
     */
    public function __construct($id, $userId)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->eventService         = new EventService();
        $this->clientService        = new ClientService();
        $this->campaignService      = new CampaignService();
        $this->logSendMailService   = new LogSendMailService();

        $this->redisCampaignClients  = sprintf(config('redis.campaign.clients'), $id);
        $this->redisCampaignMailSent = sprintf(config('redis.campaign.mail_sents'), $id);
        $this->redisCampaignStatus = sprintf(config('redis.campaign.status'), $id);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger('run campaign');

        Auth::loginUsingId($this->userId);

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

        Redis::set($this->redisCampaignStatus, $campaign::STATUS_RUNNING);

        // Email send
        if (!$this->getMailSent($campaignId)) {
            logger("ERROR in get mail sent. Campaign Pause.");
            $campaign->status = $campaign::STATUS_PAUSED;

            $this->stop($campaign);
            return;
        }

        // Load client
        if (!$this->getClient($campaign)) {
            logger("No client to send mail. Campaign stopped.");
            $campaign->status = $campaign::STATUS_FINISHED;

            $this->stop($campaign);
            return;
        }

        // Event data
        $eventVariables = $this->eventService->generateVariables($campaign->event_id);

        do {
            $client = Redis::rpop($this->redisCampaignClients);

            if (!blank($client)) {
                if (Redis::get($this->redisCampaignStatus) != $campaign::STATUS_RUNNING) {
                    logger('CAMPAIGN_IS_NOT_RUNNING');
                    return;
                }

                $arClient = json_decode($client, true);

                $clientVariables = $this->clientService->generateVariables($arClient);

                $arVariables = array_merge($eventVariables, $clientVariables);
                // Replace variable on mail content
                $mailContent = $this->campaignService->replaceVariables($campaign->mail_content, $arVariables);

                // Send mail
                $mailData = [
                    'subject' => $campaign->mail_subject,
                    'content' => $mailContent,
                ];

                try {
                    Mail::to($clientVariables['CLIENT_EMAIL'])->send(new MailCampaign($mailData));
                    $status = 'SUCCESS';
                } catch (\Throwable $th) {
                    $status = 'FAILED';
                    logger(' Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
                }

                // Save log
                $this->logSendMailService->attributes = [
                    'campaign_id' => $campaign->id,
                    'client_id' => $arClient['id'],
                    'email' => $arClient['email'],
                    'subject' => $campaign->mail_subject,
                    'content' => $mailContent,
                    'status' => $status,
                    'error' => '',
                    'sent_at' => now(),
                ];

                $this->logSendMailService->store();

                // Sleep
                sleep(1);
            }
        } while ( !blank($client) );

        $campaign->status = $campaign::STATUS_FINISHED;
        $this->stop($campaign);
        return;
    }

    private function getMailSent($campaignId)
    {
        try {
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

            return true;

        } catch (\Throwable $th) {
            logger(' Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
            return false;
        }
    }

    private function getClient($campaign)
    {
        try {
            $eventId = $campaign->event_id;
            $filters = unserialize($campaign->filter_client);

            $page = 1;
            $count = 0;

            $this->clientService->attributes['orderBy'] = 'id';
            $this->clientService->attributes['orderDesc'] = false;
            $this->clientService->attributes['filters'] = $filters;

            do {
                $this->clientService->attributes['page'] = $page++;

                $result = $this->clientService->getClientsByEventId($eventId);

                foreach ($result as $client) {
                    $email = $client->email;

                    if (!empty($email) && !Redis::sismember($this->redisCampaignMailSent, $email)) {
                        $count++;
                        Redis::lpush($this->redisCampaignClients, json_encode($client));
                    }
                }
            } while ( !blank($result) );

            return $count > 0;

        } catch (\Throwable $th) {
            logger('Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());

            return false;
        }
    }

    public function failed(Throwable $exception)
    {
        logger($exception);
    }

    public function stop($campaign)
    {
        Redis::del($this->redisCampaignStatus);
        Redis::del($this->redisCampaignMailSent);
        Redis::del($this->redisCampaignClients);

        $campaign->status = $campaign::STATUS_FINISHED;
        $campaign->save();
    }
}
