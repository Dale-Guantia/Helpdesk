<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Status;
use App\Models\Priority;
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
        $currentUser = Auth::user();

        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                ->default(auth()->id()),
                Forms\Components\Section::make('Ticket Details')
                    ->collapsible()
                    // ->collapsed(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord ? false : true)
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->label('Message')
                                    ->required()
                                    ->maxLength(65535),
                                Forms\Components\FileUpload::make('attachment')
                                    ->multiple()
                                    ->preserveFilenames()
                                    ->reorderable()
                                    ->openable()
                                    ->downloadable()
                                    ->directory('attachments/' . date('m-y'))
                                    ->maxSize(25000)
                                    ->rules([
                                        'mimes:jpeg,png,pdf,doc,docx,xls,xlsx,zip,txt,mp4', // List all allowed extensions directly
                                    ])
                                    ->validationMessages([
                                        'mimes' => 'Sorry, but this file type is not supported. Please upload one of the following: JPEG, PNG, PDF, Word Document, Excel Spreadsheet, ZIP, or Text File.',
                                    ])
                            ])->columnSpan(2),

                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Select::make('department_id')
                                    ->label('Department of concern')
                                    ->live()
                                    ->required()
                                    ->placeholder('Select a Department')
                                    ->relationship('department', 'department_name')
                                    ->default(1),
                                Forms\Components\Select::make('office_id')
                                    ->label('Division of concern')
                                    ->placeholder('Select a Division')
                                    ->live()
                                    ->required()
                                    ->disabled(fn (Forms\Get $get) => !$get('department_id'))
                                    ->relationship('office', 'office_name', function ($query, Forms\Get $get, $livewire) use ($currentUser) {
                                        $department_id = $get('department_id');

                                        // If no department is selected, ensure no offices are returned
                                        if (empty($department_id)) {
                                            return $query->whereRaw('1 = 0'); // Returns an empty query
                                        }
                                        // Base query: offices belonging to the selected department
                                        $query->where('department_id', $department_id);

                                        // Add conditional logic specific to user roles and form type (CreateRecord)
                                        if ($livewire instanceof \Filament\Resources\Pages\CreateRecord) {
                                            if ($currentUser->isAgent()) {
                                                if ($currentUser->office_id && Office::where('id', $currentUser->office_id)->where('department_id', $department_id)->exists()) {
                                                    $query->where('id', '!=', $currentUser->office_id);
                                                }
                                            }
                                        }
                                        return $query; // Return the modified query
                                    }),
                                Forms\Components\Select::make('problem_category_id')
                                    ->label('Issue Category')
                                    ->live()
                                    ->required()
                                    ->options(function (Forms\Get $get) {
                                        $office_id = $get('office_id');
                                        $department_id = $get('department_id');

                                        if (!$department_id) {
                                            return []; // Cannot select issue without department
                                        }

                                        $hasDivisions = Office::where('department_id', $department_id)->exists();

                                        if ($hasDivisions) {
                                            if (!$office_id) {
                                                return []; // If department has divisions, must select office first
                                            }
                                            $issues = ProblemCategory::where('office_id', $office_id)
                                                ->pluck('category_name', 'id')
                                                ->toArray();
                                        } else {
                                            // If department has no divisions, get issues directly under department (where office_id is null)
                                            $issues = ProblemCategory::whereNull('office_id')
                                                ->where('department_id', $department_id)
                                                ->pluck('category_name', 'id')
                                                ->toArray();
                                        }

                                        // Always add 'Other' option
                                        $issues['other'] = 'Other';

                                        return $issues ?: ['' => 'No Issue Category Available'];
                                    })
                                    ->disabled(function (Forms\Get $get) {
                                        $department_id = $get('department_id');
                                        $office_id = $get('office_id');
                                        $hasDivisions = Office::where('department_id', $department_id)->exists();

                                        // Disable if no department is selected OR
                                        // if department has divisions but no office is selected
                                        return !$department_id || ($hasDivisions && !$office_id);
                                    })
                                    ->dehydrated(fn ($state) => $state !== 'other'),
                                Forms\Components\TextInput::make('custom_problem_category')
                                    ->label('If other, please specify')
                                    ->required()
                                    ->visible(fn (Forms\Get $get) => $get('problem_category_id') === 'other')
                                    ->requiredIf('problem_category_id', 'other'),
                                Forms\Components\Select::make('priority_id')
                                    ->label('Priority Level')
                                    ->required()
                                    ->default(3)
                                    ->relationship('priority', 'priority_name'),
                                Forms\Components\Select::make('assigned_to_user_id')
                                    ->label('Assign To')
                                    ->placeholder('Unassigned')
                                    ->live()
                                    ->default(null) // Assuming unassigned by default
                                    ->relationship('assignedToUser', 'name', function ($query, Forms\Get $get) use ($currentUser) {
                                        $selectedOfficeId = $get('office_id');
                                        // Base query: Staff, Division Head, Super Admin roles
                                        $query->whereIn('role', [User::ROLE_STAFF, User::ROLE_DIVISION_HEAD, User::ROLE_SUPER_ADMIN]);

                                        if (empty($selectedOfficeId)) {
                                            return $query->whereRaw('1 = 0'); // Returns an empty result set
                                        }
                                        // Filter by office_id if selected (for Super Admin)
                                        if ($currentUser->isSuperAdmin()) {
                                            if ($selectedOfficeId) {
                                                $query->where('office_id', $selectedOfficeId);
                                            }
                                        }
                                        // For Division Heads, restrict to their own office
                                        elseif ($currentUser->isDivisionHead()) {
                                            $query->where('office_id', $currentUser->office_id);
                                        }
                                        // Exclude the current user from being assigned to themselves
                                        $query->where('id', '!=', $currentUser->id);

                                        return $query;
                                    })
                                    ->preload()
                                    // Hide if not Super Admin or Division Head
                                    ->hidden(fn () => !$currentUser->isSuperAdmin() && !$currentUser->isDivisionHead())
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get, $livewire) {
                                        if ($livewire instanceof \Filament\Resources\Pages\CreateRecord) {
                                            $set('status_id', !empty($state) ? 1 : 3); // 1: Assigned/Pending, 3: Unassigned
                                        } else if ($livewire instanceof \Filament\Resources\Pages\EditRecord) {
                                            $currentStatusId = $get('status_id');
                                            if ($currentStatusId !== 2) { // '2' is the ID for 'Resolved' status
                                                if (!empty($state)) {
                                                    $set('status_id', 1); // Set to 'Pending' (or your 'Assigned' status ID)
                                                } else {
                                                    $set('status_id', 3); // Set to 'Unassigned'
                                                }
                                            }
                                        }
                                    }),
                                Forms\Components\Select::make('status_id')
                                    ->label('Status')
                                    ->live()
                                    ->relationship('status', 'status_name')
                                    ->default(3) // Default to Unassigned
                                    ->disabled(function ($livewire, $record = null) {
                                        $currentUser = Auth::user();
                                            // If it's a "create" page, disable for everyone
                                            if ($livewire instanceof \Filament\Resources\Pages\CreateRecord) {
                                                return true; // Always disabled when creating
                                            }

                                            // If it's an "edit" page, enable only for agents
                                            if ($livewire instanceof \Filament\Resources\Pages\EditRecord) {
                                                // Check if the current user is the ticket creator
                                                $isTicketCreator = $record && $record->user_id === $currentUser->id;

                                            // Ticket creators (who are not Super Admins) cannot edit status
                                            if ($isTicketCreator) {
                                                return true;
                                            }

                                            return !$currentUser?->isAgent();
                                        }
                                        return true; // Default to disabled if context is unknown
                                    })
                                    ->dehydrated(),
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
                // Tables\Columns\TextColumn::make('assignedToUser.name')
                //     ->label('Assigned To')
                //     ->default('Unassigned') // Display 'Unassigned' if null
                //     ->searchable()
                //     ->sortable()
                //     ->formatStateUsing(function ($state, $record) {
                //         return $record->assigned_to_user_id  === auth()->id() ? 'You' : $state;
                //     }),
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
                    ->color(fn ($record): string => $record->priority->badge_color ?? 'secondary')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status.status_name')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record): string => $record->status->badge_color ?? 'secondary')
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
                    ->options(Status::pluck('status_name', 'status_name'))
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
                    ->options(Priority::pluck('priority_name', 'priority_name'))
                    ->query(function ($query, array $data) {
                        $values = $data['values'] ?? [];
                        if (count($values)) {
                            $query->whereHas('priority', function ($q) use ($values) {
                                $q->whereIn('priority_name', $values);
                            });
                        }
                    }),
                SelectFilter::make('assigned_to_user_id')
                    ->label('Assigned To')
                    ->relationship('assignedToUser', 'name')
                    ->placeholder('All')
                    ->default(null)
                    ->searchable()
                    ->options(function () {
                        // Only show agents (Super Admin, Division Head, Staff)
                        return User::whereIn('role', [User::ROLE_DIVISION_HEAD, User::ROLE_STAFF])
                                   ->pluck('name', 'id');
                    })
                    ->hidden(fn () => !Auth::user()->isSuperAdmin() && !Auth::user()->isDivisionHead()), // Only visible to Super Admin and Division Head
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(''),
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->hidden(fn ($record) => $record->status_id === 2 || !auth()->user()->isSuperAdmin() && !auth()->user()->isDivisionHead() && auth()->id() !== $record->user_id),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->hidden(fn ($record) => !auth()->user()->isSuperAdmin() && auth()->id() != $record->user_id),
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
        $query = static::getModel()::query();

        // Super Admin: See all records
        if ($user->isSuperAdmin()) {
            return $query;
        }

        // Division Head: See tickets in their office (assigned or unassigned) OR tickets created by them
        elseif ($user->isDivisionHead()) {
            return $query->where(function (Builder $q) use ($user) {
                // Tickets for their division/office (unassigned or assigned to staff in their division)
                $q->where(function (Builder $inner) use ($user) {
                    if ($user->office_id !== null) {
                        $inner->where('office_id', $user->office_id);
                    } else {
                        // Fallback if Division Head's office_id is null, use department_id
                        $inner->where('department_id', $user->department_id);
                    }
                });
                // OR tickets created by the Division Head themselves
                $q->orWhere('user_id', $user->id);
            });
        }

        // Staff: See tickets assigned to them OR tickets created by them
        elseif ($user->isStaff()) {
            return $query->where(function (Builder $q) use ($user) {
                $q->where('assigned_to_user_id', $user->id) // Assigned to this staff
                  ->orWhere('user_id', $user->id); // Created by this staff
            });
        }

        // Employee: See only tickets created by them
        elseif ($user->isEmployee()) {
            return $query->where('user_id', $user->id);
        }

        // Default: If somehow an unknown role, return empty
        return $query->whereRaw('0=1');
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
