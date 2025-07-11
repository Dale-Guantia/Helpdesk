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
use App\Models\Department;
use App\Models\Office;
use Filament\Tables\Filters\SelectFilter;

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

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\Select::make('department_id')
                        ->label('Department')
                        ->live()
                        ->required()
                        ->default(auth()->user()->isSuperAdmin() ? null : auth()->user()->department_id)
                        ->relationship('department', 'department_name')
                        ->prefixIcon('heroicon-m-building-office-2')
                        ->disabled(fn () => !auth()->user()->isSuperAdmin())
                        ->dehydrated(),
                    Forms\Components\Select::make('office_id')
                        ->label('Division')
                        ->required()
                        ->default(auth()->user()->isSuperAdmin() ? null : auth()->user()->office_id)
                        ->relationship('office', 'office_name', function ($query, \Filament\Forms\Get $get) {
                            $department_id = $get('department_id');
                            return $query->where('department_id', $department_id);
                        })
                        ->prefixIcon('heroicon-m-building-office-2')
                        ->disabled(fn (\Filament\Forms\Get $get) =>
                            !auth()->user()->isSuperAdmin() || !$get('department_id')
                        )
                        ->dehydrated(),
                    Forms\Components\TextInput::make('category_name')
                        ->label('Issue Description')
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
                Tables\Columns\TextColumn::make('department.department_name')
                    ->label('Department')
                    ->limit(20)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('office.office_name')
                    ->label('Division')
                    ->default('N/A')
                    ->limit(20)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category_name')
                    ->label('Issue Description')
                    ->limit(20)
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
                SelectFilter::make('office_id')
                    ->label('Division')
                    ->multiple()
                    ->options(Office::pluck('office_name', 'office_name'))
                    ->query(function ($query, array $data) {
                        $values = $data['values'] ?? [];
                        if (count($values)) {
                            $query->whereHas('office', function ($q) use ($values) {
                                $q->whereIn('office_name', $values);
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

    protected static function getTableQuery(): Builder
    {
        $user = auth()->user();

        // Super admin sees all records
        if ($user->isSuperAdmin()) {
            return static::getModel()::query();
        }
        // HRDO Division Head with office_id set: filter by office
        elseif ($user->isDivisionHead() && $user->office_id !== null) {
            return static::getModel()::where('office_id', $user->office_id);
        }
        else {
            // HRDO Division Head without office_id or other roles: filter by department
            return static::getModel()::where('department_id', $user->department_id);
        }

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
