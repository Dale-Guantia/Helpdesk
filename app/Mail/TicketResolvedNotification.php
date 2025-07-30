<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketResolvedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Ticket $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Ticket Has Been Resolved: #' . $this->ticket->reference_id,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tickets.resolved',
            with: ['ticket' => $this->ticket],
        );
    }

    public function attachments(): array { return []; }
}
