<?php

namespace App\Listeners;

use Illuminate\Support\Str;
use App\Mail\MailResetPassword;
use App\Events\UserCreatedEvent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\Api\PasswordResetService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailResetPassword
{
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
        $url = config('app.fe_url') . '/reset-password/?token=' . $token;

        $mailData = [
            'title' => 'Mail from ItSolutionStuff.com',
            'body' => 'This is for testing email using smtp.',
            'url' => $url,
        ];

        Mail::to($event->user->email)->send(new MailResetPassword($mailData));
    }
}
