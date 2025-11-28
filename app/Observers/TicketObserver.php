<?php

namespace App\Observers;

use App\Mail\NewTicketToDivisionNotification;
use App\Mail\TicketAssignedToStaffNotification;
use App\Mail\TicketReopenedNotification;
use App\Mail\TicketResolvedNotification;
use App\Mail\TicketTransferredToDivisionNotification;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth; // To get the user performing the action
use Illuminate\Support\Facades\Mail;

class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     * Rule 1: Notify division heads on new ticket to their office/division.
     */
    public function created(Ticket $ticket): void
    {
        $this->notifyDivisionHeadsOfOffice($ticket, NewTicketToDivisionNotification::class);
    }

    /**
     * Handle the Ticket "updated" event.
     * Rules 2, 3, 4, 5.
     */
    public function updated(Ticket $ticket): void
    {
        // Rule 2: Notify division heads if ticket is transferred to them from other division
        if ($ticket->isDirty('office_id')) {
            $this->handleTicketTransfer($ticket);
        }

        // Rule 3: Notify staff if a new ticket is assigned to them
        if ($ticket->isDirty('assigned_to_user_id')) {
            $this->handleStaffAssignment($ticket);
        }

        // Rule 4: Notify the ticket creator if their ticket is set to "resolved"
        // Rule 5: Notify resolver/DH if ticket is reopened by creator
        if ($ticket->isDirty('status_id')) {
            $oldStatusId = $ticket->getOriginal('status_id');
            $newStatusId = $ticket->status_id;

            // Rule 4: Ticket resolved
            if ($newStatusId == Ticket::STATUS_RESOLVED && $oldStatusId != Ticket::STATUS_RESOLVED) {
                $this->handleTicketResolved($ticket);
            }
            // Rule 5: Ticket reopened
            elseif ($newStatusId == Ticket::STATUS_PENDING|| $newStatusId == Ticket::STATUS_REOPENED) {
                // Check if it was previously resolved or closed, meaning it's now 'reopened'
                if ($oldStatusId == Ticket::STATUS_RESOLVED) {
                     $this->handleTicketReopened($ticket);
                }
            }
        }
    }

    /**
     * Helper to notify all division heads of a ticket's office.
     *
     * @param Ticket $ticket
     * @param string $mailableClass The class of the Mailable to send
     * @param array $extraMailableParams Additional parameters for the Mailable constructor
     */
    protected function notifyDivisionHeadsOfOffice(Ticket $ticket, string $mailableClass, array $extraMailableParams = []): void
    {
        if (!$ticket->office_id) {
            return; // Cannot notify division heads if no office is set on the ticket
        }

        $divisionHeads = User::query()
            ->where('role', User::ROLE_DIVISION_HEAD)
            ->where('office_id', $ticket->office_id)
            ->get();

        foreach ($divisionHeads as $divisionHead) {
            if (empty($divisionHead->email)) {
                continue; // Skip users with no email address
            }

            Mail::to($divisionHead->email)->send(new $mailableClass($ticket, $divisionHead, ...$extraMailableParams));
        }
    }

    /**
     * Handle ticket transfer notification (Rule 2).
     */
    protected function handleTicketTransfer(Ticket $ticket): void
    {
        // Get the user who performed the transfer. This will be the currently authenticated user.
        $transferredBy = Auth::check() ? Auth::user() : null;

        $this->notifyDivisionHeadsOfOffice(
            $ticket,
            TicketTransferredToDivisionNotification::class,
            [$transferredBy] // Pass the transferredBy user to the Mailable
        );
    }

    /**
     * Handle staff assignment notification (Rule 3).
     */
    protected function handleStaffAssignment(Ticket $ticket): void
    {
        $assignedUser = $ticket->assignedToUser;

        if ($assignedUser && $assignedUser->isStaff()) {
            Mail::to($assignedUser->email)->send(new TicketAssignedToStaffNotification($ticket, $assignedUser));
        }
    }

    /**
     * Handle ticket resolved notification (Rule 4).
     */
    protected function handleTicketResolved(Ticket $ticket): void
    {
        // Notify the ticket creator
        $creator = $ticket->creator;
        if ($creator) {
            Mail::to($creator->email)->send(new TicketResolvedNotification($ticket));
        }

        // Important: Ensure resolved_by_user_id is set when resolving the ticket.
        // This should happen in your Filament action/form that sets the status to 'resolved'.
        // Example: $ticket->resolved_by_user_id = auth()->id(); $ticket->save();
    }

    /**
     * Handle ticket reopened notification (Rule 5).
     */
    protected function handleTicketReopened(Ticket $ticket): void
    {
        // Notify the user who previously resolved the ticket
        $resolvedByUser = $ticket->resolvedByUser;
        if ($resolvedByUser) {
            Mail::to($resolvedByUser->email)->send(new TicketReopenedNotification($ticket, $resolvedByUser));
        }
    }

    // --- Other potential Observer methods you might use ---
    // public function deleting(Ticket $ticket): void {}
    // public function restored(Ticket $ticket): void {}
    // public function forceDeleted(Ticket $ticket): void {}
}
