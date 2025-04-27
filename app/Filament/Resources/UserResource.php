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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel()
                    ->prefix('+63')
                    ->minLength(10) // At least 11 digits
                    ->maxLength(11) // Up to 11 digits
                    ->numeric() // Only allow numbers
                    ->dehydrated(fn ($state) => filled($state)) // Send only if field has value
                    ->visible(fn ($record) => true) // Always visible (edit or create)
                    ->placeholder('Enter phone number')
                    ->prefixIcon('heroicon-o-phone')
                    ->autocomplete('tel')
                    ->rules([
                        'nullable',
                        'regex:/^[0-9]{10,11}$/'
                    ]),
                // Forms\Components\Select::make('office_id')
                //     ->options(Office::all()
                //         ->pluck('name', 'id'))
                //     ->searchable(),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->confirmed()
                    ->revealable()
                    ->maxLength(255)
                    ->dehydrated(fn ($state) => filled($state)) // Only send if filled
                    ->visible(fn ($record) => $record === null) // Show only when creating
                    ->required(fn ($record) => $record === null) // Required only when creating
                    ->same('passwordConfirmation') // Must match the confirmation field
                    ->autocomplete('new-password'),
                Forms\Components\TextInput::make('passwordConfirmation')
                    ->label('Confirm Password')
                    ->password()
                    ->revealable()
                    ->required(fn ($state, $get) => filled($get('password'))) // Only require if password is filled
                    ->autocomplete('new-password'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
