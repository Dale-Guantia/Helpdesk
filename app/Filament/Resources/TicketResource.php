<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\ProblemCategory;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;


class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                ->default(auth()->id()),
                Forms\Components\Section::make('Ticket Details')
                    ->collapsed(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord ? false : true)
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->label('Message')
                                    ->maxLength(65535),
                                Forms\Components\FileUpload::make('attachment')
                                    ->multiple()
                                    ->preserveFilenames()
                                    ->reorderable()
                                    ->openable()
                                    ->downloadable()
                                    ->directory('attachments/' . date('m-y'))
                                    ->maxSize(25000)
                                    ->acceptedFileTypes([
                                        'image/jpeg',
                                        'image/png',
                                        'application/pdf',
                                        'application/msword',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
                                        'application/vnd.ms-excel',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                                        'application/zip',
                                        'text/plain',
                                    ]),
                            ])->columnSpan(2),

                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Select::make('office_id')
                                    ->label('Office of concern')
                                    ->reactive()
                                    ->relationship('office', 'office_name')
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('status_id', $state ? 1 : 3);
                                    }),
                                Forms\Components\Select::make('problem_category_id')
                                    ->label('Problem Category')
                                    ->options(function (callable $get) {
                                        $office_id = $get('office_id');

                                        if (!$office_id) {
                                            return [];
                                        }
                                        return ProblemCategory::where('office_id', $office_id)
                                            ->pluck('category_name', 'id')
                                            ->toArray();
                                    })
                                    ->disabled(fn (callable $get) => !$get('office_id')), // Disable if office_id is not selected
                                Forms\Components\Select::make('priority_id')
                                    ->label('Priority Level')
                                    ->default(3)
                                    ->relationship('priority', 'priority_name'),
                                Forms\Components\Select::make('status_id')
                                    ->label('Status')
                                    ->default(3)
                                    ->relationship('status', 'status_name')
                                    ->visible(function (Forms\Get $get, Forms\Set $set, ?Ticket $record) {
                                        $user = Auth::user();

                                        if (!$user) {
                                            return false;
                                        }

                                        // Always visible for Super Admins
                                        if ($user->isSuperAdmin()) {
                                            return true;
                                        }

                                        // HRDO Admins: Only show if editing someone else's ticket
                                        if ($user->isHrdoDivisionHead()) {
                                            // If creating (no record yet) or the ticket is created by themselves, hide
                                            if (!$record || $record->user_id === $user->id) {
                                                return false;
                                            }
                                            return true;
                                        }

                                        // Employees: always hidden
                                        return false;
                                    }),
                        ])->columnSpan(1)
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(static::getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('reference_id')
                    ->label('Ticket ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->title)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created by')
                    ->translateLabel()
                    ->grow(false)
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        return $record->user_id === auth()->id() ? 'You' : $state;
                    }),
                Tables\Columns\TextColumn::make('problemCategory.category_name')
                    ->label('Type of Issue')
                    ->default('N/A')
                    ->searchable()
                    ->sortable()
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->problemCategory?->category_name ?? '-'),
                Tables\Columns\TextColumn::make('priority.priority_name')
                    ->label('Priority Level')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'High' => 'danger',
                        'Medium' => 'warning',
                        'Low' => 'info',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status.status_name')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Resolved' => 'success',
                        'Unassigned' => 'gray',
                    })
                    ->searchable()
                    ->sortable()
            ])
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'Pending' => 'Pending',
                        'Resolved' => 'Resolved',
                        'Unassigned' => 'Unassigned',
                    ])
                    ->query(function ($query, array $data) {
                        $values = $data['values'] ?? [];
                        if (count($values)) {
                            $query->whereHas('status', function ($q) use ($values) {
                                $q->whereIn('status_name', $values);
                            });
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(''),
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->hidden(fn ($record) => !auth()->user()->isSuperAdmin() && !auth()->user()->isHrdoDivisionHead() && auth()->id() !== $record->user_id),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->hidden(fn ($record) => !auth()->user()->isSuperAdmin() && auth()->id() !== $record->user_id),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function getTableQuery(): Builder
    {
        $user = auth()->user();
        // Super Admin account, return all records
        if ($user->isSuperAdmin()) {
            return static::getModel()::query();
        }
        // HRDO admin account: return tickets in their office OR tickets created by them
        elseif ($user->isHrdoDivisionHead()) {
            return static::getModel()::where(function ($query) use ($user) {
                $query->where('office_id', $user->office_id)
                    ->orWhere('user_id', $user->id);
            });
        }
        // Employee account: return tickets created by them
        return static::getModel()::where(function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });
    }


    public static function getRelations(): array
    {
        return [
            RelationManagers\CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
            'view' => Pages\ViewTicket::route('/{record}'),
        ];
    }
}
