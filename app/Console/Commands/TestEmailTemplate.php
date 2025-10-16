<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailTemplate extends Command
{
    protected $signature = 'email:test {email}';
    protected $description = 'Test password reset email template';

    public function handle()
    {
        $email = $this->argument('email');
        
        Mail::send('emails.password-reset-test', [
            'token' => 'test-token-123',
            'email' => $email
        ], function ($message) use ($email) {
            $message->to($email)
                    ->subject('Password Reset – O2Mart')
                    ->from(config('mail.from.address'), config('mail.from.name'));
        });

        $this->info("Test email sent to: {$email}");
    }
}