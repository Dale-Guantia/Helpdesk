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
                                Forms\Components\Select::make('problem_category_id')
                                    ->label('Issue Category')
                                    ->live()
                                    ->required()
                                    ->placeholder('Select an Issue Category')
                                    ->options(function (Forms\Get $get): array {
                                        $departmentId = $get('department_id');
                                        $officeId = $get('office_id');

                                        if (empty($departmentId)) {
                                            return [];
                                        }

                                        $query = ProblemCategory::query();

                                        if (!empty($officeId)) {
                                            $query->where('office_id', $officeId);
                                        } else {
                                            $query->where('department_id', $departmentId);
                                        }

                                        $categories = $query->pluck('category_name', 'id')->toArray();

                                        $categories['other'] = 'Other';

                                        return $categories;
                                    })
                                    ->dehydrated(fn ($state) => $state !== 'other')
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        // This function runs when problem_category_id changes

                                        // Clear custom_problem_category if not 'other'
                                        if ($state !== 'other') {
                                            $set('custom_problem_category', null);
                                        }

                                        // Handle auto-setting office_id based on selected category
                                        if ($state === 'other') {
                                            // If 'other' is selected, clear office_id to allow manual selection or leave null
                                            $set('office_id', null);
                                            // You might also want to explicitly enable the office_id if it was disabled
                                            // by a previous problem_category selection, though the disabled() logic should handle this.
                                        } else {
                                            // If a specific problem category is selected
                                            $problemCategory = ProblemCategory::find($state);

                                            if ($problemCategory && $problemCategory->office_id) {
                                                // If the problem category has an associated office, set it
                                                $set('office_id', $problemCategory->office_id);
                                            } else {
                                                $set('office_id', null);
                                            }
                                        }
                                    }),
                                Forms\Components\TextInput::make('custom_problem_category')
                                    ->label('If other, please specify')
                                    ->required()
                                    ->maxLength(255)
                                    ->visible(fn (Forms\Get $get) => $get('problem_category_id') === 'other')
                                    ->requiredIf('problem_category_id', 'other'),
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
                                    ->placeholder('Select a Department')
                                    ->live() // Essential for reactivity
                                    ->required()
                                    ->relationship('department', 'department_name')
                                    ->default(1) // Keep your default if desired
                                    ->afterStateUpdated(function (Forms\Set $set) {
                                        $set('problem_category_id', null);
                                        $set('office_id', null);
                                        $set('custom_problem_category', null);
                                    }),
                                Forms\Components\Select::make('office_id')
                                    ->label('Division of concern')
                                    ->placeholder('Select a Division')
                                    ->live()
                                    // Disabled if no department or if a category with a pre-set office_id is selected
                                    ->disabled(function (Forms\Get $get) {
                                        $departmentId = $get('department_id');
                                        $problemCategoryId = $get('problem_category_id');

                                        // If no department is selected, always disable
                                        if (empty($departmentId)) {
                                            return true;
                                        }

                                        // If 'other' problem category is selected, allow user to pick office (so not disabled)
                                        if ($problemCategoryId === 'other') {
                                            return false;
                                        }

                                        // Check if the selected problem category has an associated office_id
                                        // and if that office_id matches the currently selected office_id.
                                        // If it does, disable the field because it's auto-selected.
                                        $problemCategory = ProblemCategory::find($problemCategoryId);
                                        if ($problemCategory && $problemCategory->office_id) {
                                            return true; // Disable, as it's automatically filled
                                        }

                                        // Otherwise, allow selection
                                        return false;
                                    })
                                    ->relationship('office', 'office_name', function ($query, Forms\Get $get) {
                                        $departmentId = $get('department_id');
                                        // Filter offices by the selected department
                                        if ($departmentId) {
                                            return $query->where('department_id', $departmentId);
                                        }
                                        return $query->whereRaw('1 = 0'); // No options if no department
                                    })
                                    ->dehydrated(),
                                Forms\Components\Select::make('priority_id')
                                    ->label('Priority Level')
                                    ->relationship('priority', 'priority_name')
                                    ->hidden(fn () => !$currentUser->isSuperAdmin() && !$currentUser->isDivisionHead()),
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
                    ->default('N/A')
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
