<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketReopenedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Ticket $ticket;
    public ?User $reopenedToUser; // Division Head OR Staff who previously resolved it

    public function __construct(Ticket $ticket, ?User $reopenedToUser)
    {
        $this->ticket = $ticket;
        $this->reopenedToUser = $reopenedToUser;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ticket Reopened: #' . $this->ticket->reference_id,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tickets.reopened',
            with: ['ticket' => $this->ticket, 'reopenedToUser' => $this->reopenedToUser],
        );
    }

    public function attachments(): array { return []; }
}
