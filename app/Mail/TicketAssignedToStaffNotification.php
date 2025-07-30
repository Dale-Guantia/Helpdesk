<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketAssignedToStaffNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Ticket $ticket;
    public User $staff;

    public function __construct(Ticket $ticket, User $staff)
    {
        $this->ticket = $ticket;
        $this->staff = $staff;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Ticket Assigned To You: #' . $this->ticket->reference_id,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tickets.assigned-to-staff',
            with: ['ticket' => $this->ticket, 'staff' => $this->staff],
        );
    }

    public function attachments(): array { return []; }
}
