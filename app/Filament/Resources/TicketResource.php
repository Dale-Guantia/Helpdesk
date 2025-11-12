<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Priority;
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
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;

class TicketResource extends Resource
{
    /**
     * Define the URL to redirect to after a record is updated.
     *
     * @param Model|null $record The model instance that was updated.
     * @return string The URL to redirect to.
     */

    public static function getRedirectUrl(?Model $record = null): string
    {
        return static::getUrl('view', ['record' => $record->id]);
    }

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
                                    ->searchable()
                                    ->placeholder('Select an Issue Category')
                                    ->hidden(fn (Forms\Get $get) => $get('is_other'))
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

                                        return $categories;
                                    })
                                    ->disabled(function (?Model $record): bool {
                                        if (!$record) {
                                            return false;
                                        }

                                        $user = auth()->user();

                                        $isCreator = $user->id === $record->user_id;

                                        return !($isCreator || $user->isSuperAdmin() || $user->isDepartmentHead() || $user->isDivisionHead());
                                    })
                                    ->dehydrated(fn ($state) => $state !== 'other')
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        $problemCategory = ProblemCategory::find($state);

                                        if ($problemCategory && $problemCategory->office_id) {
                                            // If the problem category has an associated office, set it
                                            $set('office_id', $problemCategory->office_id);
                                        } else {
                                            $set('office_id', null);
                                        }
                                    }),
                                Forms\Components\TextInput::make('custom_problem_category')
                                    ->label('Custom Issue Category')
                                    // Use visible() to show this field only when the 'is_other' checkbox IS checked
                                    ->visible(fn (Forms\Get $get) => $get('is_other'))
                                    // Make it required only when it's visible
                                    ->required(fn (Forms\Get $get) => $get('is_other'))
                                    ->maxLength(255),
                                Forms\Components\Checkbox::make('is_other')
                                    ->label('Specify a custom category')
                                    ->live() // Makes the form update in real-time when toggled
                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                        // Reset the other fields when the checkbox is toggled to prevent old data issues
                                        $set('problem_category_id', null);
                                        $set('custom_problem_category', null);
                                    }),
                                Forms\Components\Textarea::make('description')
                                    ->label('Message')
                                    ->required()
                                    ->maxLength(65535)
                                    ->disabled(function (?Model $record): bool {
                                        if (!$record) {
                                            return false;
                                        }

                                        $user = auth()->user();

                                        $isCreator = $user->id === $record->user_id;

                                        return !($isCreator || $user->isSuperAdmin());
                                    })
                                    ->dehydrated(),
                                Forms\Components\FileUpload::make('attachment')
                                    ->multiple()
                                    ->directory('attachments/' . date('m-y'))
                                    ->reorderable()
                                    ->openable()
                                    ->downloadable()
                                    ->dehydrated(true)
                                    ->maxSize(25000)
                                    ->rules([
                                        'mimes:jpeg,png,pdf,doc,docx,xls,xlsx,zip,txt,mp4,webp',
                                    ])
                                    ->validationMessages([
                                        'mimes' => 'Sorry, but this file type is not supported. Please upload one of the following: JPEG, PNG, PDF, Word Document, Excel Spreadsheet, ZIP, or Text File.',
                                    ])
                                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file) {
                                        // Compress only if it's an image
                                        if (str_starts_with($file->getMimeType(), 'image/')) {
                                            $image = Image::read($file->getRealPath());
                                            $image->save($file->getRealPath(), 80);
                                             $image->scaleDown(1920);
                                        }

                                        // Return a string path, not an array
                                        return $file->store('attachments/' . date('m-y'), 'public');
                                    })

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
                                    ->disabled(function (?Model $record): bool {
                                        if (!$record) {
                                            return false;
                                        }

                                        $user = auth()->user();

                                        $isCreator = $user->id === $record->user_id;

                                        return !($isCreator || $user->isSuperAdmin() || $user->isDepartmentHead() || $user->isDivisionHead());
                                    })
                                    ->afterStateUpdated(function (Forms\Set $set) {
                                        $set('problem_category_id', null);
                                        $set('office_id', null);
                                        $set('custom_problem_category', null);
                                    })
                                    ->dehydrated(),
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
                                    ->hidden(fn () => !$currentUser->isSuperAdmin() && !$currentUser->isDivisionHead() && !$currentUser->isDepartmentHead()),
                                Forms\Components\Select::make('assigned_to_user_id')
                                    ->label('Assign To')
                                    ->placeholder('Unassigned')
                                    ->live()
                                    ->default(null) // Assuming unassigned by default
                                    ->relationship('assignedToUser', 'name', function ($query, Forms\Get $get) use ($currentUser) {
                                        $selectedOfficeId = $get('office_id');
                                        // Base query: Staff, Division Head, Super Admin roles
                                        $query->whereIn('role', [User::ROLE_STAFF, User::ROLE_DIVISION_HEAD, User::ROLE_DEPT_HEAD]);

                                        if (empty($selectedOfficeId)) {
                                            return $query->whereRaw('1 = 0'); // Returns an empty result set
                                        }
                                        // Filter by office_id if selected (for Super Admin and Division Head)
                                        if ($currentUser->isSuperAdmin() || $currentUser->isDivisionHead() || $currentUser->isDepartmentHead()) {
                                            if ($selectedOfficeId) {
                                                $query->where('office_id', $selectedOfficeId);
                                            }
                                        }
                                        // // For Division Heads, restrict to their own office
                                        // elseif ($currentUser->isDivisionHead()) {
                                        //     $query->where('office_id', $currentUser->office_id);
                                        // }
                                        // Exclude the current user from being assigned to themselves
                                        $query->where('id', '!=', $currentUser->id);

                                        return $query;
                                    })
                                    ->preload()
                                    // Hide if not Super Admin or Division Head
                                    ->hidden(fn () => !$currentUser->isSuperAdmin() && !$currentUser->isDepartmentHead() && !$currentUser->isDivisionHead())
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get, $livewire) {
                                        if ($livewire instanceof \Filament\Resources\Pages\CreateRecord) {
                                            $set('status_id', !empty($state) ? Ticket::STATUS_PENDING : Ticket::STATUS_UNASSIGNED); // 1: Assigned/Pending, 3: Unassigned
                                        } else if ($livewire instanceof \Filament\Resources\Pages\EditRecord) {
                                            $currentStatusId = $get('status_id');
                                            if ($currentStatusId !== Ticket::STATUS_RESOLVED) { // '2' is the ID for 'Resolved' status
                                                if (!empty($state)) {
                                                    $set('status_id', Ticket::STATUS_PENDING); // Set to 'Pending' (or your 'Assigned' status ID)
                                                } else {
                                                    $set('status_id', Ticket::STATUS_UNASSIGNED); // Set to 'Unassigned'
                                                }
                                            }
                                        }
                                    }),
                                Forms\Components\Select::make('status_id')
                                    ->label('Status')
                                    ->live()
                                    ->options(Ticket::STATUSES)
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
                                    ->dehydrated()
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_id')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => Ticket::STATUSES[$state] ?? 'Unknown')
                    ->color(fn (Ticket $record): string => match ($record->status_id) {
                        Ticket::STATUS_PENDING => 'warning',
                        Ticket::STATUS_RESOLVED => 'success',
                        Ticket::STATUS_UNASSIGNED => 'gray',
                        Ticket::STATUS_REOPENED => 'primary',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('overdue_status')
                    ->label('Overdue')
                    ->badge()
                    ->color(function (Ticket $record): string {
                        // All overdue = red
                        if ($record->isOverdue()) {
                            return 'danger';
                        }

                        // On Track (resolved on time)
                        if ($record->isResolved() && !$record->isOverdue()) {
                            return 'success';
                        }

                        // Pending, Unassigned, or Reopened (active, not overdue)
                        if (in_array($record->status_id, [
                            Ticket::STATUS_PENDING,
                            Ticket::STATUS_UNASSIGNED,
                            Ticket::STATUS_REOPENED,
                        ])) {
                            return 'warning';
                        }

                        return 'secondary';
                    }),
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
                SelectFilter::make('status_id')
                    ->label('Status')
                    ->multiple()
                    ->options(Ticket::STATUSES)
                    ->query(function ($query, array $data) {
                        $selectedStatusIds = $data['values'] ?? [];
                        if (count($selectedStatusIds)) {
                            $query->whereIn('status_id', $selectedStatusIds);
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
                    ->default(null)
                    ->searchable()
                    ->options(function () {
                        // Only show agents (Super Admin, Division Head, Staff)
                        return User::whereIn('role', [User::ROLE_DEPT_HEAD, User::ROLE_DIVISION_HEAD, User::ROLE_STAFF])
                                   ->pluck('name', 'id');
                    })
                    ->hidden(fn () => !Auth::user()->isSuperAdmin() && !Auth::user()->isDepartmentHead() && !Auth::user()->isDivisionHead()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(''),
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->hidden(fn ($record) => $record->status_id === Ticket::STATUS_RESOLVED || !auth()->user()->isAgent() && auth()->id() !== $record->user_id),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->hidden(fn ($record) => !auth()->user()->isSuperAdmin() && !auth()->user()->isDepartmentHead() && auth()->id() != $record->user_id),
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

        // Department and Division Head: See tickets in their office (assigned or unassigned) OR tickets created by them
        elseif ($user->isDivisionHead() || $user->isDepartmentHead()) {
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
            'view' => Pages\ViewTicket::route('/{record}'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
