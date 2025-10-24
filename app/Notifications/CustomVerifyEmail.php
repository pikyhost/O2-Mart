<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmail
{
    public function toMail($notifiable)
    {
        // Generate the backend verification URL with signature
        $backendVerificationUrl = $this->verificationUrl($notifiable);
        
        // Parse the backend URL to get the signature and other parameters
        $parsedUrl = parse_url($backendVerificationUrl);
        parse_str($parsedUrl['query'] ?? '', $queryParams);
        
        // Build the frontend verification URL
        // Frontend should be: https://mk3bel.o2mart.net/verify-email?id=X&hash=Y&expires=Z&signature=ABC
        $frontendUrl = config('app.frontend_url');
        $verificationUrl = $frontendUrl . '/verify-email?' . http_build_query([
            'id' => $notifiable->getKey(),
            'hash' => sha1($notifiable->getEmailForVerification()),
            'expires' => $queryParams['expires'] ?? '',
            'signature' => $queryParams['signature'] ?? ''
        ]);

        return (new MailMessage)
            ->subject('Verify Email Address – O2Mart')
            ->view('emails.verify-email', [
                'user' => $notifiable,
                'verificationUrl' => $verificationUrl
            ]);
    }
}