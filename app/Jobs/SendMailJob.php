<?php

namespace App\Jobs;

use Throwable;
use App\Mail\MailCampaign;
use Illuminate\Bus\Queueable;
use App\Enums\MessageCodeEnum;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Services\Api\LogSendMailService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;

    protected $mailData;

    protected $mailLogData;

    protected $logSendMailService;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $mailData, $mailLogData)
    {
        $this->email    = $email;
        $this->mailData = $mailData;
        $this->mailLogData  = $mailLogData;
        $this->logSendMailService = new LogSendMailService();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger('Start job send mail');

        $email = $this->email;

        $result = Mail::to($email)->send(new MailCampaign($this->mailData));

        if ($result) {
            $status = MessageCodeEnum::SUCCESS;
        } else {
            $status = MessageCodeEnum::ERROR;
        }

        $this->logSendMailService->attributes = $this->mailLogData;
        $this->logSendMailService->attributes['status'] = $status;
        $this->logSendMailService->store();
    }

    /**
     * The job failed to process.
     */
    public function failed(Throwable $exception): void
    {
        $this->logSendMailService->attributes = $this->mailLogData;
        $this->logSendMailService->attributes['status'] = MessageCodeEnum::ERROR;
        $this->logSendMailService->store();
    }
}
