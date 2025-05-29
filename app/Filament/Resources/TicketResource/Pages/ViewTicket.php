<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use App\Models\Ticket;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Infolists\Components\ViewEntry;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        // If the record is not loaded yet, return nothing.
        if (!$this->record) {
            return [];
        }

        // Hide edit button if ticket is resolved (status_id = 2)
        if ($this->record->status_id === 2) {
            return [];
        }

        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Grid::make()
                    ->schema([
                        Components\Section::make('Ticket Details')
                            ->schema([
                                Components\TextEntry::make('id')->label('Ticket ID'),
                                Components\TextEntry::make('title')->label('Title'),
                                Components\TextEntry::make('description')->label('Message'),
                                ViewEntry::make('attachment')
                                    ->label('Attachments:')
                                    ->view('filament.components.attachment-list')
                                    ->visible(fn (Ticket $record) => is_array($record->attachment) && count($record->attachment) > 0),
                            ])->columnSpan(9), // 8/12 columns = ~66%
                        Components\Section::make()
                            ->schema([
                                Components\TextEntry::make('office.office_name')
                                    ->label('Office of concern')
                                    ->default('N/A'),
                                Components\TextEntry::make('problemCategory.category_name')
                                    ->label('Problem category')
                                    ->default('N/A'),
                                Components\TextEntry::make('priority.priority_name')
                                    ->label('Priority Level')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'High' => 'danger',
                                        'Medium' => 'warning',
                                        'Low' => 'info',
                                    }),
                                Components\TextEntry::make('status.status_name')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Pending' => 'warning',
                                        'Resolved' => 'success',
                                        'Unassigned' => 'gray',
                                    }),
                                Components\TextEntry::make('created_at')->dateTime('F j, Y g:i A')->label('Created at'),
                                Components\TextEntry::make('updated_at')->dateTime('F j, Y g:i A')->label('Updated at'),
                            ])->columnSpan(3), // 4/12 columns = ~33%
                    ])
                    ->columns(12), // total columns for grid system
            ]);
    }

}
