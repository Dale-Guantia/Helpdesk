<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Office;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Carbon;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('viewAny', User::class);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->prefixIcon('heroicon-m-user'),
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->email()
                    ->maxLength(255)
                    ->prefixIcon('heroicon-m-envelope'),
                Forms\Components\TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel()
                    ->maxLength(11)
                    ->minLength(10)
                    ->prefixIcon('heroicon-m-phone'),
                Forms\Components\Select::make('department_id')
                    ->relationship('department', 'department_name')
                    ->prefixIcon('heroicon-m-building-office-2')
                    ->required()
                    ->reactive(),
                Forms\Components\Select::make('office_id')
                    ->label('Division')
                    ->relationship('office', 'office_name')
                    ->prefixIcon('heroicon-m-building-office-2')
                    ->disabled(fn (callable $get) => !$get('department_id'))
                    ->options(function (callable $get) {
                        $department_id = $get('department_id');

                        if (!$department_id) {
                            return [];
                        }
                        return Office::where('department_id', $department_id)
                            ->pluck('office_name', 'id')
                            ->toArray();
                    }),
                Forms\Components\Select::make('role')
                    ->options(User::ROLES)
                    ->required()
                    ->prefixIcon('heroicon-m-users'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->confirmed()
                    ->required()
                    ->revealable()
                    ->visible(fn ($record) => $record === null)
                    ->prefixIcon('heroicon-m-lock-closed'),
                Forms\Components\TextInput::make('password_confirmation')
                    ->password()
                    ->required()
                    ->revealable()
                    ->visible(fn ($record) => $record === null)
                    ->prefixIcon('heroicon-m-lock-closed'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Activate/Deactivate User')
                    ->default(true)
                    ->inline(false)
                    ->required()
                    ->reactive()
                    ->dehydrated(fn ($state) => filled($state)) // Only send if field has value
                    ->visible(fn ($record) => true) // Always visible (edit or create)
                    ->rules([
                        'boolean',
                        'nullable',
                    ]),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->query(static::getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('User ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        1 => 'Super Admin',
                        2 => 'Division Head',
                        3 => 'HRDO Staff',
                        4 => 'Guest',
                    })
                    ->badge() // Optional: to show it as a badge style
                    ->color(fn ($state) => match ($state) {
                        1 => 'warning',  // Yellow for Super Admin
                        2 => 'primary',     // Blue for Division Head
                        3 => 'info',  // Purple for HRDO Staff
                        4 => 'gray',  // Gray for Guest
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_active')
                    ->label('Status')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive')
                    ->badge() // This shows it as a badge
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('department.department_name')
                    ->label('Department')
                    ->default('N/A')
                    ->limit(20)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('office.office_name')
                    ->label('Division')
                    ->default('N/A')
                    ->limit(20)
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('User Role')
                    ->multiple()
                    ->options(User::ROLES),
                SelectFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ]),
                SelectFilter::make('department_id')
                    ->label('Department')
                    ->multiple()
                    ->relationship('department', 'department_name'),
                SelectFilter::make('office_id')
                    ->label('Division')
                    ->multiple()
                    ->relationship('office', 'office_name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(''),
                Tables\Actions\DeleteAction::make()
                    ->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // protected static function getTableQuery(): Builder
    // {
    //     $user = auth()->user();

    //     // If admin, return all records
    //     if ($user->isSuperAdmin()) {
    //         return static::getModel()::query();
    //     }

    //     // Otherwise filter by office_id
    //     return static::getModel()::where('office_id', $user->office_id);
    // }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
