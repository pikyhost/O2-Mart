<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class VerifyNewsletterSubscription extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected \App\Models\NewsletterSubscriber $subscriber)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'newsletter.verify',
            now()->addHours(24),
            ['id' => $this->subscriber->id, 'hash' => sha1($this->subscriber->email)]
        );

        return (new MailMessage)
            ->subject('Verify Your Newsletter Subscription')
            ->line('Thank you for subscribing to our newsletter!')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email', $verificationUrl)
            ->line('If you did not subscribe, please ignore this email.');
    }
}
