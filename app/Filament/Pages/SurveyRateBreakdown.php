<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\Survey;
use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;

class SurveyRateBreakdown extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.survey-rate-breakdown';

    public User $user;
    public $surveyCounts = [];

    public function mount(User $user): void
    {
        $this->user = $user;

        // Preload rating counts for performance
        $this->surveyCounts = Survey::selectRaw('
            SUM(CASE WHEN responsiveness_rating = "very_dissatisfied" THEN 1 ELSE 0 END
            + CASE WHEN timeliness_rating = "very_dissatisfied" THEN 1 ELSE 0 END
            + CASE WHEN communication_rating = "very_dissatisfied" THEN 1 ELSE 0 END
            ) as very_dissatisfied_count,

            SUM(CASE WHEN responsiveness_rating = "dissatisfied" THEN 1 ELSE 0 END
            + CASE WHEN timeliness_rating = "dissatisfied" THEN 1 ELSE 0 END
            + CASE WHEN communication_rating = "dissatisfied" THEN 1 ELSE 0 END
            ) as dissatisfied_count,

            SUM(CASE WHEN responsiveness_rating = "satisfied" THEN 1 ELSE 0 END
            + CASE WHEN timeliness_rating = "satisfied" THEN 1 ELSE 0 END
            + CASE WHEN communication_rating = "satisfied" THEN 1 ELSE 0 END
            ) as satisfied_count,

            SUM(CASE WHEN responsiveness_rating = "very_satisfied" THEN 1 ELSE 0 END
            + CASE WHEN timeliness_rating = "very_satisfied" THEN 1 ELSE 0 END
            + CASE WHEN communication_rating = "very_satisfied" THEN 1 ELSE 0 END
            ) as very_satisfied_count,

            COUNT(*) * 3 as total
        ')
        ->where('user_id', $user->id)
        ->first()
        ->toArray();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->user)
            ->schema([
                Section::make("Staff Information")
                    ->schema([
                        TextEntry::make('name')->label('Staff Name'),
                        TextEntry::make('email')->label('Email'),
                    ])
                    ->columns(2),

                Section::make("Survey Breakdown")
                    ->schema([
                        TextEntry::make('total')
                            ->label('Total Surveys')
                            ->default($this->surveyCounts['total'] ?? 0),

                        TextEntry::make('very_dissatisfied')
                            ->label('Very Dissatisfied')
                            ->default($this->surveyCounts['very_dissatisfied_count'] ?? 0),

                        TextEntry::make('dissatisfied')
                            ->label('Dissatisfied')
                            ->default($this->surveyCounts['dissatisfied_count'] ?? 0),

                        TextEntry::make('satisfied')
                            ->label('Satisfied')
                            ->default($this->surveyCounts['satisfied_count'] ?? 0),

                        TextEntry::make('very_satisfied')
                            ->label('Very Satisfied')
                            ->default($this->surveyCounts['very_satisfied_count'] ?? 0),
                    ])
                    ->columns(2),
            ]);
    }
}

