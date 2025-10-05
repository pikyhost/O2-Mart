<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPassword extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = url(config('app.frontend_url') . '/password/reset/' . $this->token . '?email=' . urlencode($notifiable->email));

        return (new MailMessage)
            ->subject('Password Reset – O2Mart')
            ->view('emails.password-reset', [
                'resetUrl' => $resetUrl,
                'user' => $notifiable
            ]);
    }
}