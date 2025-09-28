<?php

namespace App\Notifications;

use App\Helpers\GeneralHelper;
use App\Models\ContactMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ContactMessageMailNotification extends Notification
{
    public ContactMessage $message;

    public function __construct(ContactMessage $message)
    {
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('ContactMessage.Subject'))
            ->greeting(__('ContactMessage.Greeting'))
            ->line(__('ContactMessage.Intro'))
            ->line(__('ContactMessage.Name') . ' ' . $this->message->name)
            ->line(__('ContactMessage.Email') . ' ' . $this->message->email)
            ->line(__('ContactMessage.Phone') . ' ' . $this->message->phone)
            ->line(__('ContactMessage.SubjectLabel') . ' ' . ($this->message->subject ?? __('ContactMessage.NotAvailable')))
            ->line(__('ContactMessage.Message'))
            ->line($this->message->message)
            ->line(__('ContactMessage.IP') . ' ' . $this->message->ip_address);
    }
}
