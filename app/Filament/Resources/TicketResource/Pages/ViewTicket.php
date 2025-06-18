<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use App\Models\Ticket;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Infolists\Components\ViewEntry;
use App\Filament\Actions\ReopenTicketAction;
use Illuminate\Support\Facades\Auth;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        // If the record is not loaded yet, return nothing.
        if (!$this->record) {
            return [];
        }

        $actions = [];

        $editAction = Actions\EditAction::make()
            ->label('Edit / Add Comment')
            ->visible(function (Ticket $record): bool {
                $currentUser = auth()->user();
                return $record->status_id !== 2 || ($currentUser && method_exists($currentUser, 'isSuperAdmin') && $currentUser->isSuperAdmin());
            });

        $reopenAction = ReopenTicketAction::make('reopen_ticket');

        $actions[] = $reopenAction;

        $actions[] = $editAction;

        return $actions;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Grid::make()
                    ->schema([
                        Components\Section::make('Ticket Details')
                            ->schema([
                                Components\TextEntry::make('reference_id')->label('Reference ID'),
                                Components\TextEntry::make('title')->label('Title'),
                                Components\TextEntry::make('description')->label('Message'),
                                ViewEntry::make('attachment')
                                    ->label('Attachments:')
                                    ->view('filament.components.attachment-list')
                                    ->visible(fn (Ticket $record) => is_array($record->attachment) && count($record->attachment) > 0),
                            ])->columnSpan(9), // 8/12 columns = ~66%
                        Components\Section::make()
                            ->schema([
                                Components\TextEntry::make('user.name')
                                    ->label('Created by')
                                    ->formatStateUsing(function ($state, $record) {
                                        return $record->user_id === auth()->id() ? 'You' : $state;
                                    }),
                                Components\TextEntry::make('office.office_name')
                                    ->label('Division of concern')
                                    ->default('N/A'),
                                Components\TextEntry::make('problemCategory.category_name')
                                    ->label('Problem category')
                                    ->default('N/A')
                                    ->formatStateUsing(function (string $state, $record): string {
                                        if ($record->problem_category_id === null) {
                                            return $record->custom_problem_category ?? 'N/A';
                                        }
                                        return $state;
                                    }),
                                Components\TextEntry::make('priority.priority_name')
                                    ->label('Priority Level')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'High' => 'danger',
                                        'Medium' => 'warning',
                                        'Low' => 'primary',
                                    }),
                                Components\TextEntry::make('status.status_name')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Pending' => 'warning',
                                        'Resolved' => 'success',
                                        'Unassigned' => 'gray',
                                        'Reopened' => 'primary'
                                    }),
                                Components\TextEntry::make('created_at')->dateTime('F j, Y g:i A')->label('Created at'),
                                Components\TextEntry::make('updated_at')->dateTime('F j, Y g:i A')->label('Updated at'),
                            ])->columnSpan(3), // 4/12 columns = ~33%
                    ])
                    ->columns(12), // total columns for grid system
            ]);
    }

}
