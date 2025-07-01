<?php

namespace App\Notifications;

use App\Mail\NewReplyReceived;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewTicketReply extends Notification implements ShouldQueue
{
    use Queueable;

    public Comment $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail']; // We are only sending this via email
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): NewReplyReceived
    {
        // Pass the comment and the recipient to the Mailable
        return (new NewReplyReceived($this->comment, $notifiable))
            ->to($notifiable->email);
    }
}
