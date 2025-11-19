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
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class SurveyResource extends Resource
{
    public static function getNavigationLabel(): string
    {
        return 'Survey Responses';
    }

    public static function getModelLabel(): string
    {
        return 'Survey Response';
    }

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';

    protected static ?string $model = Survey::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Survey Details')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Submission Date')
                            ->dateTime('M d, Y - h:i:s A'),
                        TextEntry::make('staff.name')
                            ->label('Staff Rated'),
                        TextEntry::make('service.category_name')
                            ->label('Service Received'),
                    ])->columnSpan(1),

                Section::make('Service Ratings')
                    ->schema([
                        TextEntry::make('responsiveness_rating')
                            ->label('Responsiveness')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Very Dissatisfied' => 'danger',
                                'Dissatisfied' => 'warning',
                                'Satisfied' => 'primary',
                                'Very Satisfied' => 'success',
                            }),
                        TextEntry::make('timeliness_rating')
                            ->label('Timeliness')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Very Dissatisfied' => 'danger',
                                'Dissatisfied' => 'warning',
                                'Satisfied' => 'primary',
                                'Very Satisfied' => 'success',
                            }),
                        TextEntry::make('communication_rating')
                            ->label('Communication')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Very Dissatisfied' => 'danger',
                                'Dissatisfied' => 'warning',
                                'Satisfied' => 'primary',
                                'Very Satisfied' => 'success',
                            }),
                    ])->columnSpan(1),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('service.category_name')
                    ->label('Service')
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('staff.name')
                    ->label('Staff Rated')
                    ->searchable(),
                TextColumn::make('responsiveness_rating')
                    ->label('Responsiveness')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Very Dissatisfied' => 'danger',
                        'Dissatisfied' => 'warning',
                        'Satisfied' => 'primary',
                        'Very Satisfied' => 'success',
                    }),
                TextColumn::make('timeliness_rating')
                    ->label('Timeliness')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Very Dissatisfied' => 'danger',
                        'Dissatisfied' => 'warning',
                        'Satisfied' => 'primary',
                        'Very Satisfied' => 'success',
                    }),
                TextColumn::make('communication_rating')
                    ->label('Communication')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Very Dissatisfied' => 'danger',
                        'Dissatisfied' => 'warning',
                        'Satisfied' => 'primary',
                        'Very Satisfied' => 'success',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()
                    ->label('View Details')
                    ->color('primary'),
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
            'view' => Pages\ViewSurvey::route('/{record}'),
            // 'create' => Pages\CreateSurvey::route('/create'),
            // 'edit' => Pages\EditSurvey::route('/{record}/edit'),
        ];
    }
}
