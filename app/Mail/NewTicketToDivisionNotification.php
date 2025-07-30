<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewTicketToDivisionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Ticket $ticket;
    public User $divisionHead;

    public function __construct(Ticket $ticket, User $divisionHead)
    {
        $this->ticket = $ticket;
        $this->divisionHead = $divisionHead;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Ticket for Your Division: #' . $this->ticket->reference_id,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tickets.new-to-division',
            with: ['ticket' => $this->ticket, 'divisionHead' => $this->divisionHead],
        );
    }

    public function attachments(): array { return []; }
}
