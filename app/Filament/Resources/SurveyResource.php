<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SurveyResource\Pages;
use App\Models\Survey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Radio;
use Filament\Tables\Columns\TextColumn;

class SurveyResource extends Resource
{
    public static function getNavigationLabel(): string
    {
        return 'Survey Responses';
    }

    public static function getModelLabel(): string
    {
        return 'Survey Responses';
    }

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';

    protected static ?string $model = Survey::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Survey Details') // Create the first section
                    ->description('Enter the basic details of the survey.')
                    ->schema([
                        DatePicker::make('submission_date')
                            ->required(),
                        Select::make('problem_category_id')
                            ->relationship('service', 'category_name')
                            ->required(),
                        Select::make('user_id')
                            ->label('Attended by')
                            ->relationship('staff', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Textarea::make('suggestions')
                            ->label('Suggestions (Optional)')
                            ->nullable(),
                    ])->columnSpan(1),
                Forms\Components\Section::make('Service Ratings') // Create the second section
                    ->description('Please rate the quality of service provided.')
                    ->schema([
                        Radio::make('responsiveness_rating')
                            ->label('RESPONSIVENESS')
                            ->options([
                                'Very Dissatisfied' => 'Very Dissatisfied',
                                'Dissatisfied' => 'Dissatisfied',
                                'Satisfied' => 'Satisfied',
                                'Very Satisfied' => 'Very Satisfied',
                            ]),
                        Radio::make('timeliness_rating')
                            ->label('TIMELINESS')
                            ->options([
                                'Very Dissatisfied' => 'Very Dissatisfied',
                                'Dissatisfied' => 'Dissatisfied',
                                'Satisfied' => 'Satisfied',
                                'Very Satisfied' => 'Very Satisfied',
                            ]),
                        Radio::make('communication_rating')
                            ->label('COMMUNICATION')
                            ->options([
                                'Very Dissatisfied' => 'Very Dissatisfied',
                                'Dissatisfied' => 'Dissatisfied',
                                'Satisfied' => 'Satisfied',
                                'Very Satisfied' => 'Very Satisfied',
                            ]),
                    ])->columnSpan(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('submission_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('service.category_name')
                    ->label('Service Received')
                    ->searchable(),
                TextColumn::make('staff.name')
                    ->label('Staff Rated')
                    ->searchable(),
                TextColumn::make('responsiveness_rating'),
                TextColumn::make('timeliness_rating'),
                TextColumn::make('communication_rating'),
            ])
            ->filters([
                //
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSurveys::route('/'),
            'create' => Pages\CreateSurvey::route('/create'),
            'view' => Pages\ViewSurvey::route('/{record}'),
            'edit' => Pages\EditSurvey::route('/{record}/edit'),
        ];
    }
}
