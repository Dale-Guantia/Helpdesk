<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficeResource\Pages;
use App\Models\Department;
use App\Models\Office;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class OfficeResource extends Resource
{
    protected static ?string $model = Office::class;

    public static function label(): string
    {
        return 'Divisions';
    }

    public static function getModelLabel(): string
    {
        return 'Division';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Divisions';
    }

    public static function getNavigationLabel(): string
    {
        return 'Divisions';
    }

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('department_id')
                    ->relationship('department', 'department_name')
                    ->required()
                    ->prefixIcon('heroicon-m-building-office-2'),
                Forms\Components\TextInput::make('office_name')
                    ->label('Division Name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Division ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.department_name')
                    ->label('Department')
                    ->limit(30)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('office_name')
                    ->label('Division')
                    ->limit(30)
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('department_id')
                    ->label('Department')
                    ->multiple()
                    ->options(Department::pluck('department_name', 'department_name'))
                    ->query(function ($query, array $data) {
                        $values = $data['values'] ?? [];
                        if (count($values)) {
                            $query->whereHas('department', function ($q) use ($values) {
                                $q->whereIn('department_name', $values);
                            });
                        }
                    }),
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
            'index' => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffice::route('/create'),
            'edit' => Pages\EditOffice::route('/{record}/edit'),
        ];
    }
}
