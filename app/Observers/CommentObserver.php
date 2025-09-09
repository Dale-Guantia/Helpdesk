<?php

namespace App\Observers;

use App\Models\Comment;
use App\Models\User;
use App\Notifications\NewTicketReply;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class CommentObserver
{
    /**
     * Handle the Comment "created" event.
     */
    public function created(Comment $comment): void
    {
        // Eager load relationships for efficiency
        $comment->load('user', 'ticket.user', 'ticket.assignedToUser', 'ticket.office.divisionHead');

        $commentAuthor = $comment->user;
        $ticket = $comment->ticket;

        if (!$ticket) {
            Log::error('Comment ID: ' . $comment->id . ' has no associated ticket.');
            return;
        }

        // Get potential recipients
        $ticketOwner = $ticket->User;
        $assignedStaff = $ticket->assignedToUser;

        $recipients = collect();

        // Add the ticket owner if they exist.
        if ($ticketOwner) {
            $recipients->push($ticketOwner);
        }

        // Determine who should receive the notification based on assignment status.
        if ($assignedStaff) {
            // If the ticket is assigned, also notify the assigned staff.
            $recipients->push($assignedStaff);
        } else {
            // If the ticket is not assigned, we need to notify the division head.
            if ($ticket->office) {
                $divisionHead = $ticket->office->divisionHead;
                if ($divisionHead) {
                    $recipients->push($divisionHead);
                }
            }
        }

        // Get a unique list of recipients who are NOT the person who just commented.
        $uniqueRecipients = $recipients->unique('id')->reject(function ($recipient) use ($commentAuthor) {
            return $recipient->id === $commentAuthor->id;
        });

        Log::info('Attempting to send reply notifications to user IDs: ' . $uniqueRecipients->pluck('id')->implode(', '));

        // Send the notification to the final list of recipients.
        if ($uniqueRecipients->isNotEmpty()) {
            Notification::send($uniqueRecipients, new NewTicketReply($comment));
        }
    }
}
