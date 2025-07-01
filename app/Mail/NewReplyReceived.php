<?php

namespace App\Mail;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewReplyReceived extends Mailable
{
    use Queueable, SerializesModels;

    public Comment $comment;
    public User $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct(Comment $comment, User $recipient)
    {
        $this->comment = $comment;
        $this->recipient = $recipient;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Reply on Ticket #' . $this->comment->ticket->reference_id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new-reply',
        );
    }
}
