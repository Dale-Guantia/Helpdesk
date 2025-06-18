<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class ReopenTicketAction extends Action
{
    public static function make(?string $name = null): static
    {
        return parent::make($name)
            ->label('Reopen Ticket')
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->visible(function (Ticket $record): bool {
                // Ensure it's the creator and the ticket is resolved (status_id = 2)
                return auth()->id() === $record->user_id && $record->status_id === 2;
            })
            ->action(function (Ticket $record) {
                // IMPORTANT CHANGE: Set status_id to 4 for "Reopened"
                $record->status_id = 4; // Use your new "Reopened" status ID
                $record->resolved_at = null; // Clear resolved_at timestamp
                $record->save();

                // Add an automatic comment for reopening
                $record->comments()->create([
                    'user_id' => Auth::id(),
                    'comment' => 'This ticket has been reopened by the ticket creator for further discussion/resolution. Status changed from Resolved to Reopened.',
                ]);

                Notification::make()
                    ->title('Ticket Reopened')
                    ->body('The ticket has been successfully reopened.')
                    ->success()
                    ->send();
            })
            ->requiresConfirmation()
            ->modalHeading('Reopen Ticket Confirmation')
            ->modalDescription('Are you sure you want to reopen this ticket? This will allow further comments and edits.')
            ->modalSubmitActionLabel('Yes, reopen it');
    }
}
