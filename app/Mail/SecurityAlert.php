<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SecurityAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $vehicleName;
    public $driverName;
    public $incidentType;
    public $deviation;
    public $dashboardUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->vehicleName = $data['vehicleName'] ?? 'Unknown';
        $this->driverName = $data['driverName'] ?? 'System';
        $this->incidentType = $data['incidentType'] ?? 'General Alert';
        $this->deviation = $data['deviation'] ?? '0.0';
        $this->dashboardUrl = url('/dashboard');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🚨 FLEETCO: Security Breach Detected - ' . $this->vehicleName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.security-alert',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
