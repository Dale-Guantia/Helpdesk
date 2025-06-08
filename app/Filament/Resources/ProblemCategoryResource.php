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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\Select::make('department_id')
                    ->label('Department')
                    ->reactive()
                    ->relationship('department', 'department_name')
                    ->prefixIcon('heroicon-m-building-office-2')
                    ->options(function () {
                        $user = auth()->user();
                        // Admin can select all offices
                        if ($user->isSuperAdmin()) {
                            return Department::all()->pluck('department_name', 'id');
                        }
                        // Non-admin: only their own office
                        return Department::where('id', $user->department_id)->pluck('department_name', 'id');
                    })
                    ->default(auth()->user()->isSuperAdmin() ? null : auth()->user()->department_id)
                    ->disabled(!auth()->user()->isSuperAdmin()) // disable for non-admins
                    ->dehydrated()
                    ->required(),
                Forms\Components\Select::make('office_id')
                    ->label('Division')
                    ->relationship('office', 'office_name')
                    ->prefixIcon('heroicon-m-building-office-2')
                    ->options(function (callable $get) {
                        $department_id = $get('department_id');
                        $user = auth()->user();
                        // Admin can select all offices
                        if ($user->isSuperAdmin()) {
                            if (!$department_id) {
                                return [];
                            }
                            return Office::where('department_id', $department_id)
                                ->pluck('office_name', 'id')
                                ->toArray();
                        }
                        // Non-admin: only their own office
                        return Office::where('id', $user->office_id)->pluck('office_name', 'id');
                    })
                    ->default(auth()->user()->isSuperAdmin() ? null : auth()->user()->office_id)
                    ->dehydrated()
                    ->disabled(fn (callable $get) =>
                        !auth()->user()->isSuperAdmin() || !$get('department_id')
                    ), // disable for non-admins or if department is not selected
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
