<?php

namespace App\Jobs;

use App\Enums\MessageCodeEnum;
use Illuminate\Bus\Queueable;
use App\Services\Api\EventService;
use App\Services\Api\ClientService;
use Illuminate\Support\Facades\Auth;
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

    protected $pageSize = 50;

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
        if (!$this->getMailSent()) {
            logger("ERROR in get mail sent. Campaign Pause.");

            $campaign->status = $campaign::STATUS_PAUSED;
            $campaign->save();

            $this->clearRedis();
            return;
        }

        // Load client
        if (!$this->getClient($campaign)) {
            logger("No client to send mail. Campaign stopped.");

            $campaign->status = $campaign::STATUS_FINISHED;
            $campaign->save();

            $this->clearRedis();
            return;
        }

        // Event data
        $arEventVariables = $this->eventService->generateVariables($campaign->event_id);

        $totalClient = Redis::llen($this->redisCampaignClients);
        if ($totalClient == 0) {
            logger("No client to send mail. Campaign stopped.");

            $campaign->status = $campaign::STATUS_FINISHED;
            $campaign->save();

            $this->clearRedis($campaign);
            return;
        }

        for ($i = 0; $i < $totalClient; $i++) {
            // Stop this job if the campaign is not running
            if (Redis::get($this->redisCampaignStatus) != $campaign::STATUS_RUNNING) {
                $this->clearRedis();
                return;
            }

            $client = Redis::rpop($this->redisCampaignClients);

            // Generate variables
            $arClient = json_decode($client, true);
            $arClientVariables = $this->clientService->generateVariables($arClient);
            $arVariables = array_merge($arEventVariables, $arClientVariables);

            // Replace variable on mail content
            $mailContent = $this->campaignService->replaceVariables($arEventVariables['email_content'], $arVariables);

            // Data for send mail
            $mailData = [
                'subject' => $campaign->mail_subject,
                'content' => $mailContent,
            ];

            // Store log mail
            $mailLogData = [
                'campaign_id' => $campaign->id,
                'client_id' => $arClient['id'],
                'email' => $arClient['email'],
                'subject' => $campaign->mail_subject,
                'content' => $mailContent,
                'status' => MessageCodeEnum::PROCESSING,
                'error' => '',
                'sent_at' => now(),
            ];
            $this->logSendMailService->attributes = $mailLogData;
            $mailLog = $this->logSendMailService->store();

            $mailLogData['id'] = $mailLog->id;

            dispatch(new SendMailJob($arClient['email'], $mailData, $mailLogData));

            sleep(1);
        }

        logger('Campaign finished.');

        // Campaign finished
        $campaign->status = $campaign::STATUS_FINISHED;
        $campaign->save();

        $this->clearRedis();
        return;
    }

    private function getMailSent()
    {
        try {
            // Set attributes
            $service = $this->logSendMailService;

            $service->attributes['filters']['campaign_id'] = $this->id;
            $service->attributes['orderBy'] = 'id';
            $service->attributes['orderDesc'] = false;
            $service->attributes['pageSize'] = $this->pageSize;

            // Count total
            $total = $service->count();
            if ($total == 0) return true;

            // Calculate total pages
            $lastPage = ceil($total / $this->pageSize);

            for ($page = 1; $page <= $lastPage; $page++) {
                // Set current page
                $service->attributes['page'] = $page;

                $result = $service->getList();

                foreach ($result as $log) {
                    Redis::sadd($this->redisCampaignMailSent, $log->email);
                }
            }
            return true;
        } catch (\Throwable $th) {
            logger(' Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
            return false;
        }
    }

    private function getClient($campaign)
    {
        try {
            $service = $this->clientService;

            // Set attributes
            $service->attributes['orderBy'] = 'id';
            $service->attributes['orderDesc'] = false;
            $service->attributes['pageSize'] = $this->pageSize;
            $service->attributes['filters'] = unserialize($campaign->filter_client);

            $total = $service->count();
            if ($total == 0) return false;

            // Calculate total pages
            $lastPage = ceil($total / $this->pageSize);

            for ($page = 1; $page <= $lastPage; $page++) {
                // Set current page
                $service->attributes['page'] = $page;

                $result = $service->getClientsByEventId($campaign->event_id);

                foreach ($result as $client) {
                    $email = $client->email;

                    if (!empty($email) && !Redis::sismember($this->redisCampaignMailSent, $email)) {
                        Redis::lpush($this->redisCampaignClients, json_encode($client));
                    }
                }
            }
            return true;
        } catch (\Throwable $th) {
            logger('Error: ' . $th->getMessage() . ' on file: ' . $th->getFile() . ':' . $th->getLine());
            return false;
        }
    }

    public function failed(Throwable $exception)
    {
        logger($exception);
    }

    public function clearRedis()
    {
        Redis::del($this->redisCampaignStatus);
        Redis::del($this->redisCampaignMailSent);
        Redis::del($this->redisCampaignClients);
    }
}
