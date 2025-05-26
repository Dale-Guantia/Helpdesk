<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProblemCategoryResource\Pages;
use App\Models\ProblemCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Office;


class ProblemCategoryResource extends Resource
{
    protected static ?string $model = ProblemCategory::class;

    protected static ?string $navigationLabel = 'Issues';

    public static function label(): string
    {
        return 'Issue';
    }

    public static function getModelLabel(): string
    {
        return 'Issue';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Issues';
    }

    public static function getNavigationLabel(): string
    {
        return 'Issues';
    }


    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('office_id')
                    ->label('Division')
                    ->relationship('office', 'office_name')
                    ->prefixIcon('heroicon-m-building-office-2')
                    ->options(function () {
                        $user = auth()->user();
                        // Admin can select all offices
                        if ($user->isSuperAdmin()) {
                            return Office::all()->pluck('office_name', 'id');
                        }
                        // Non-admin: only their own office
                        return Office::where('id', $user->office_id)->pluck('office_name', 'id');
                    })
                    ->default(auth()->user()->isSuperAdmin() ? null : auth()->user()->office_id)
                    ->disabled(!auth()->user()->isSuperAdmin()) // disable for non-admins
                    ->required(),
                Forms\Components\TextInput::make('category_name')
                    ->label('Type of Issue')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(static::getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Issue ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('office.office_name')
                    ->label('Division Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category_name')
                    ->label('Type of Issue')
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
            'index' => Pages\ListProblemCategories::route('/'),
            'create' => Pages\CreateProblemCategory::route('/create'),
            'edit' => Pages\EditProblemCategory::route('/{record}/edit'),
        ];
    }
}
