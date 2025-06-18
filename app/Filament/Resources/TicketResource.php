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
use App\Models\Office;
use App\Models\ProblemCategory;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;


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
                                Forms\Components\Select::make('department_id')
                                    ->label('Department of concern')
                                    ->placeholder('Select a Department')
                                    ->reactive()
                                    ->relationship('department', 'department_name')
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('status_id', $state ? 1 : 3);
                                    }),
                                Forms\Components\Select::make('office_id')
                                    ->label('Division of concern')
                                    ->reactive()
                                    ->options(function (callable $get) {
                                        $department_id = $get('department_id');

                                        if (!$department_id) {
                                            return [];
                                        }

                                        $division = Office::where('department_id', $department_id)
                                            ->pluck('office_name', 'id')
                                            ->toArray();

                                        return $division ?: ['' => 'No Division Available'];
                                    })
                                    ->disabled(fn (callable $get) => !$get('department_id')),
                                Forms\Components\Select::make('problem_category_id')
                                    ->label('Issue Category')
                                    ->live()
                                    ->disabled(fn (callable $get) => !$get('department_id'))
                                    ->options(function (callable $get) {
                                        $office_id = $get('office_id');
                                        $department_id = $get('department_id');

                                        if (!$department_id) {
                                            return [];
                                        }

                                        // Check if the department has any offices
                                        $hasDivisions = Office::where('department_id', $department_id)->exists();

                                        if ($hasDivisions) {
                                            // If department has divisions, require an office to be selected
                                            if (!$office_id) {
                                                return ['' => 'Select Division First'];
                                            }

                                            $issues = ProblemCategory::where('office_id', $office_id)
                                                ->pluck('category_name', 'id')
                                                ->toArray() + ['other' => 'Other'];

                                            return $issues ?: ['' => 'No Issue Category Available'];
                                        } else {
                                            // If no divisions, get issues directly under department (office_id is null)
                                            $issues = ProblemCategory::whereNull('office_id')
                                                ->where('department_id', $department_id)
                                                ->pluck('category_name', 'id')
                                                ->toArray() + ['other' => 'Other'];

                                            return $issues ?: ['' => 'No Issue Category Available'];
                                        }
                                    })
                                    ->dehydrated(fn ($state) => $state !== 'other'),
                                Forms\Components\TextInput::make('custom_problem_category')
                                    ->label('If other, please specify')
                                    ->required()
                                    ->visible(fn (Forms\Get $get) => $get('problem_category_id') === 'other')
                                    ->requiredIf('problem_category_id', 'other'),
                                Forms\Components\Select::make('priority_id')
                                    ->label('Priority Level')
                                    ->default(3)
                                    ->relationship('priority', 'priority_name'),
                                Forms\Components\Select::make('status_id')
                                    ->label('Status')
                                    ->relationship('status', 'status_name')
                                    ->disabled(fn () => !Auth::user()?->isSuperAdmin())
                                    ->dehydrated(true)
                                    ->required()
                                    ->default(function (Forms\Get $get) {
                                        $department_id = $get('department_id');
                                        if ($department_id == 1) {
                                            return 1; // Replace with the ID of "Pending"
                                        }
                                        return 3; // Replace with the ID of "Unassigned"
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
                    ->label('Issue Description')
                    ->default('N/A')
                    ->searchable()
                    ->sortable()
                    ->limit(20)
                    ->copyable()
                    ->tooltip(function ($record) {
                        if ($record->problem_category_id === null) {
                            // If problem_category_id is null, use custom_problem_category
                            return $record->custom_problem_category ?? 'N/A';
                        }
                        // Otherwise, use problemCategory.category_name, with a fallback to '-'
                        return $record->problemCategory?->category_name ?? 'N/A';
                    })
                    ->formatStateUsing(function (string $state, $record): string {
                        if ($record->problem_category_id === null) {
                            return $record->custom_problem_category ?? 'N/A';
                        }
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('priority.priority_name')
                    ->label('Priority Level')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'High' => 'danger',
                        'Medium' => 'warning',
                        'Low' => 'primary',
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
                        'Reopened' => 'primary'
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($state) {
                        return Carbon::parse($state)->format('h:i A - m/d/y');
                    }),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($state) {
                        return Carbon::parse($state)->format('h:i A - m/d/y');
                    }),
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
                SelectFilter::make('priority')
                    ->multiple()
                    ->options([
                        'High' => 'High',
                        'Medium' => 'Medium',
                        'Low' => 'Low',
                    ])
                    ->query(function ($query, array $data) {
                        $values = $data['values'] ?? [];
                        if (count($values)) {
                            $query->whereHas('priority', function ($q) use ($values) {
                                $q->whereIn('priority_name', $values);
                            });
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(''),
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->hidden(fn ($record) => $record->status_id === 2 || !auth()->user()->isSuperAdmin() && !auth()->user()->isDivisionHead() && auth()->id() !== $record->user_id),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->hidden(fn ($record) => !auth()->user()->isSuperAdmin() && auth()->id() !== $record->user_id),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('created_at', 'desc')
            ->poll('10s');
    }

    protected static function getTableQuery(): Builder
    {
        $user = auth()->user();
        // Super Admin account, return all records
        if ($user->isSuperAdmin()) {
            return static::getModel()::query();
        }
        // HRDO admin account: return tickets in their office OR tickets created by them
        elseif ($user->isDivisionHead()) {
            return static::getModel()::where(function ($query) use ($user) {
                if ($user->office_id !== null) {
                    $query->where('office_id', $user->office_id);
                } else {
                    $query->where('department_id', $user->department_id);
                }

                // Also allow tickets created by the user themselves
                $query->orWhere('user_id', $user->id);
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
