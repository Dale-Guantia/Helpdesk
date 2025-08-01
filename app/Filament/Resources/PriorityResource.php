<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriorityResource\Pages;
use App\Models\Priority;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PriorityResource extends Resource
{
    protected static ?string $model = Priority::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('priority_name')
                ->required()
                ->maxLength(255),
            ]);
    }

        public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Priority ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority_name')
                    ->label('Priority Name')
                    ->badge()
                    ->color(fn ($record): string => $record->badge_color ?? 'secondary')
                    ->searchable()
                    ->sortable(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPriorities::route('/'),
            'create' => Pages\CreatePriority::route('/create'),
            'edit' => Pages\EditPriority::route('/{record}/edit'),
        ];
    }
}
