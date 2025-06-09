<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;


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

        // Only proceed if the user is an agent and status was changed to "Resolved"
        if ($user && method_exists($user, 'isAgent') && $user->isAgent()) {
            $originalStatus = $record->getOriginal('status_id');

            // Replace '2' with your actual "Resolved" status ID if needed
            if ($originalStatus !== 2 && $record->status_id == 2) {
                $user->increment('resolved_tickets_count');
            }
        }

        // If status was changed to "Resolved", add an auto-generated comment
        if ($record->wasChanged('status_id') && $record->status_id == 2) {
            if ($user && $user->isAgent()) {
                $record->comments()->create([
                    'user_id' => $user->id,
                    'comment' => 'This ticket has been resolved. For further concerns regarding this matter, please submit a new ticket. Thank you!',
                ]);
            }
        }

        // Set resolved_at if the ticket is now resolved and it's not already set
        if ($record->status_id == 2 && is_null($record->resolved_at)) {
            $record->resolved_at = now();
            $record->saveQuietly(); // avoid triggering observers/events again
        }
    }


    public static function canEdit(Model $record): bool
    {
        // Disallow editing if resolved (status_id = 2)
        return $record->status_id !== 2;
    }

    protected function authorizeAccess(): void
    {
        if ($this->record->status_id == 2) {
            abort(403, 'This ticket is already resolved and cannot be edited.');
        }

        parent::authorizeAccess();
    }

}
