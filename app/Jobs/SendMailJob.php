<?php

namespace App\Jobs;

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

    protected $logData;

    protected $logSendMailService;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $mailData, $logData)
    {
        $this->email = $email;
        $this->mailData = $mailData;
        $this->logData = $logData;
        $this->logSendMailService = new LogSendMailService();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $email = $this->email;

        $result = Mail::to($email)->send(new MailCampaign($this->mailData));

        if ($result) {
            $status = MessageCodeEnum::SUCCESS;
        } else {
            $status = MessageCodeEnum::ERROR;
        }

        $this->logSendMailService->attributes = $this->logData;
        $this->logSendMailService->attributes['status'] = $status;
        $this->logSendMailService->store();
    }
}
