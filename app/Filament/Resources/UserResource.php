<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Office;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?int $navigationSort = 6;

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
                Forms\Components\Select::make('office_id')
                    ->relationship('office', 'office_name')
                    ->prefixIcon('heroicon-m-building-office-2'),
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
                Forms\Components\Select::make('role')
                    ->options(User::ROLES)
                    ->required()
                    ->prefixIcon('heroicon-m-users'),
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
            ->query(static::getTableQuery())
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
                        2 => 'HRDO Admin',
                        3 => 'Employee',
                    })
                    ->badge() // Optional: to show it as a badge style
                    ->color(fn ($state) => match ($state) {
                        1 => 'warning',  // Yellow for Super Admin
                        2 => 'info',     // Blue for HRDO Admin
                        3 => 'gray',  // Gray for Employee
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('office.office_name')
                    ->label('Office Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_active')
                    ->label('Status')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive')
                    ->badge() // This shows it as a badge
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
            ])
            ->filters([
                //
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

    protected static function getTableQuery(): Builder
    {
        $user = auth()->user();

        // If admin, return all records
        if ($user->isSuperAdmin()) {
            return static::getModel()::query();
        }

        // Otherwise filter by office_id
        return static::getModel()::where('office_id', $user->office_id);
    }

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
