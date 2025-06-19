<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $user = Auth::user();
        $record = $this->record;

        $isAgent = $user && method_exists($user, 'isAgent') ? $user->isAgent() : false;

        // Get original status before the save operation
        $originalStatus = $record->getOriginal('status_id');
        $currentStatus = $record->status_id;

        // Only proceed if the user is an agent and status was changed to "Resolved"
        if ($isAgent && $record->wasChanged('status_id') && $currentStatus == 2) {
            $user->increment('resolved_tickets_count');
        }

        // If status was changed to "Resolved", add an auto-generated comment
        if ($record->wasChanged('status_id') && $record->status_id == 2) {
            if ($user && $user->isAgent()) {
                $record->comments()->create([
                    'user_id' => $user->id,
                    'comment' => 'This ticket has been resolved. For further concerns regarding this matter, reopen this ticket or submit a new ticket. Thank you!',
                ]);
            }
            $record->resolved_at = now();
            $record->saveQuietly(); // Save the resolved_at timestamp
        }

        // If the ticket was resolved and is now changed to "reopened" status
        // This handles cases where an agent changes it from Resolved back to Pending or another status
        if ($record->wasChanged('status_id') && $originalStatus == 2 && $currentStatus != 2) {
            $record->resolved_at = null; // Clear the resolved_at timestamp

            // Add a comment when an agent changes it from Resolved to another status
            if ($isAgent) {
                // Fetch the new status name for the comment
                $newStatusName = $record->status->status_name ?? 'N/A'; // Use null coalescing for safety
                $record->comments()->create([
                    'user_id' => $user->id,
                    'comment' => 'This ticket status was changed from Resolved to ' . $newStatusName . ' by ' . $user->name . '.',
                ]);
            }
            $record->saveQuietly(); // Save after potentially adding comment
        }
    }


    public static function canEdit(Model $record): bool
    {
        $user = Auth::user();

        // Allow editing if the ticket is NOT resolved (status_id !== 2)
        // This now includes 'Pending', 'Unassigned', 'Reopened' (ID 4)
        if ($record->status_id !== 2) {
            return true;
        }

        // If it *is* resolved (status_id === 2), only allow editing if the current user is an admin/staff
        // The ticket creator can *only* reopen it via the custom action, not directly edit when resolved.
        if ($user && $user->isSuperAdmin()) {
            return true;
        }

        // By default, if resolved and not an admin/staff, cannot edit
        return false;
    }

    protected function authorizeAccess(): void
    {
        if (!static::canEdit($this->record)) {
            abort(403, 'You are not authorized to edit this resolved ticket.');
        }

        parent::authorizeAccess();
    }

}
