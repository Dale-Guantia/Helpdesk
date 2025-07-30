<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketTransferredToDivisionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Ticket $ticket;
    public User $divisionHead;
    public ?User $transferredBy; // Nullable, as it might not always be available

    public function __construct(Ticket $ticket, User $divisionHead, ?User $transferredBy)
    {
        $this->ticket = $ticket;
        $this->divisionHead = $divisionHead;
        $this->transferredBy = $transferredBy;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ticket Transferred to Your Division: #' . $this->ticket->reference_id,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tickets.transferred-to-division',
            with: [
                'ticket' => $this->ticket,
                'divisionHead' => $this->divisionHead,
                'transferredBy' => $this->transferredBy,
            ],
        );
    }

    public function attachments(): array { return []; }
}
