<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;

class TeamInvitationMail extends Mailable
{
    private $invitation;

    /**
     * Create a new message instance.
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $acceptUrl = $this->generateAcceptUrl();

        return $this->view('emails.team-invitation')
            ->subject('Invitation to join '.config('app.name'))
            ->with([
                'invitation' => $this->invitation,
                'acceptUrl' => $acceptUrl,
            ]);
    }

    /**
     * Generate the signed URL for accepting the invitation.
     */
    private function generateAcceptUrl(): string
    {
        return URL::signedRoute(
            'invitation.accept',
            ['invitation' => $this->invitation]
        );
    }
}
