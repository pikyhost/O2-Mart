<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Test comment to verify GitHub push
class InquiryConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Inquiry $inquiry)
    {
    }

    public function build()
    {
        return $this->subject('We\'ve received your inquiry #' . $this->inquiry->id. ' O2Mart is on it')
                    ->view('emails.inquiry-confirmation');
    }
}