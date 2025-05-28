<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

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

            // Replace '5' with your actual resolved status ID
            if ($originalStatus !== 2 && $record->status_id == 2) {
                $user->increment('resolved_tickets_count');
            }
        }
    }
}
