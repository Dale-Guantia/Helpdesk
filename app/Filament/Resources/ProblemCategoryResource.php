<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProblemCategoryResource\Pages;
use App\Filament\Resources\ProblemCategoryResource\RelationManagers;
use App\Models\ProblemCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProblemCategoryResource extends Resource
{
    protected static ?string $model = ProblemCategory::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('office_id')
                    ->relationship('office', 'office_name')
                    ->prefixIcon('heroicon-m-building-office-2'),
                Forms\Components\TextInput::make('category_name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Category ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('office.office_name')
                    ->label('Office Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category_name')
                    ->label('Category Name')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListProblemCategories::route('/'),
            'create' => Pages\CreateProblemCategory::route('/create'),
            'edit' => Pages\EditProblemCategory::route('/{record}/edit'),
        ];
    }
}
