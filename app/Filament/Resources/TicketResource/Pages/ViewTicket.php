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
use Filament\Infolists\Components\Grid;
use App\Models\User;

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
            ->label('Edit Ticket')
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
                                Components\TextEntry::make('description')->label('Message'),
                                ViewEntry::make('attachment')
                                    ->label('Attachments:')
                                    ->view('filament.components.attachment-list')
                                    ->visible(fn (Ticket $record) => is_array($record->attachment) && count($record->attachment) > 0),
                            ])->columnSpan(7), // 8/12 columns = ~66%
                            Components\Section::make()
                                ->schema([
                                    // Start a new inner grid here for two-column layout
                                    Grid::make(2) // This grid will have 2 columns
                                        ->schema([
                                            Components\TextEntry::make('user.name')
                                                ->label('Created by')
                                                ->formatStateUsing(function ($state, $record) {
                                                    return $record->user_id === auth()->id() ? 'You' : $state;
                                                }),
                                            Components\TextEntry::make('assignedToUser.name')
                                                ->label('Assigned to')
                                                ->default('N/A')
                                                ->formatStateUsing(function ($state, $record) {
                                                    return $record->assigned_to_user_id === auth()->id() ? 'You' : $state;
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
                                                ->default('N/A')
                                                ->color(fn ($record): string => $record->priority->badge_color ?? 'secondary'),
                                            Components\TextEntry::make('status.status_name')
                                                ->label('Status')
                                                ->badge()
                                                ->color(fn ($record): string => $record->status->badge_color ?? 'secondary'),
                                            Components\TextEntry::make('created_at')->dateTime('m/d/y - h:i A')->label('Created at'),
                                            Components\TextEntry::make('updated_at')->dateTime('m/d/y - h:i A')->label('Updated at'),
                                        ]), // This section takes 3 out of 12 columns
                                    ])->columnSpan(5),
                    ])
                    ->columns(12), // total columns for grid system
            ]);
    }
}
