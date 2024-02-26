<?php

namespace App\Listeners;

use Illuminate\Support\Str;
use App\Mail\MailResetPassword;
use App\Events\UserCreatedEvent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\Api\PasswordResetService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailResetPassword implements ShouldQueue
{
    protected $service;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->service = new PasswordResetService();
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreatedEvent $event): void
    {
        $token = Str::random(60);

        $this->service->attributes = [
            'email' => $event->user->email,
            'token' => $token,
        ];
        $this->service->store();

        // Sendmail reset password
        $reset_password_url = config('app.reset_password_url') . '?token=' . $token;

        $mailData = [
            'title'              => 'Mail from ' . config('app.name'),
            'reset_password_url' => $reset_password_url,
        ];

        if (config('mail.disable')) {
            logger()->info('Send mail reset password ' . json_encode($mailData));
        } else {
            Mail::to($event->user->email)->send(new MailResetPassword($mailData));
        }
    }
}
