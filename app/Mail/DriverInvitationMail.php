<?php

namespace App\Mail;

use App\Models\DriverInvitation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DriverInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public DriverInvitation $invitation,
        public User $inviter
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You\'ve been invited to join ' . ($this->invitation->fleet->name ?? 'a Fleet') . ' on FleetCo',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.driver-invitation',
            with: [
                'invitation'  => $this->invitation,
                'inviter'     => $this->inviter,
                'registerUrl' => route('register.invite', ['token' => $this->invitation->token]),
                'expiresAt'   => $this->invitation->expires_at->format('M d, Y H:i'),
                'fleetName'   => $this->invitation->fleet->name ?? 'your Fleet',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
