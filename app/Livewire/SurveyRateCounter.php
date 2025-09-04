<?php

namespace App\Livewire;

use App\Models\User;
use App\Filament\Pages\SurveyRateBreakdown;
use App\Models\Survey;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Filament\Support\Contracts\TranslatableContentDriver;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;


class SurveyRateCounter extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user && ($user->isSuperAdmin() || $user->isDepartmentHead() || $user->isDivisionHead());
    }

    protected function getTableQuery(): Builder
    {
        return User::query()
            ->where('role', User::ROLE_STAFF);
    }

    protected array $ratingCounts = [];

    protected function loadRatingCounts(): void
    {
        $results = Survey::selectRaw("
                user_id,
                SUM(CASE WHEN responsiveness_rating = 'Very Dissatisfied' THEN 1 ELSE 0 END +
                    CASE WHEN timeliness_rating     = 'Very Dissatisfied' THEN 1 ELSE 0 END +
                    CASE WHEN communication_rating  = 'Very Dissatisfied' THEN 1 ELSE 0 END) AS very_dissatisfied_count,

                SUM(CASE WHEN responsiveness_rating = 'Dissatisfied' THEN 1 ELSE 0 END +
                    CASE WHEN timeliness_rating     = 'Dissatisfied' THEN 1 ELSE 0 END +
                    CASE WHEN communication_rating  = 'Dissatisfied' THEN 1 ELSE 0 END) AS dissatisfied_count,

                SUM(CASE WHEN responsiveness_rating = 'Satisfied' THEN 1 ELSE 0 END +
                    CASE WHEN timeliness_rating     = 'Satisfied' THEN 1 ELSE 0 END +
                    CASE WHEN communication_rating  = 'Satisfied' THEN 1 ELSE 0 END) AS satisfied_count,

                SUM(CASE WHEN responsiveness_rating = 'Very Satisfied' THEN 1 ELSE 0 END +
                    CASE WHEN timeliness_rating     = 'Very Satisfied' THEN 1 ELSE 0 END +
                    CASE WHEN communication_rating  = 'Very Satisfied' THEN 1 ELSE 0 END) AS very_satisfied_count
            ")
            ->groupBy('user_id')
            ->get();

        // Store in array for O(1) lookup
        $this->ratingCounts = $results->keyBy('user_id')->toArray();
    }

    protected function getDetailedRatingCountsForRecord(User $record): array
    {
        $results = Survey::selectRaw("
            responsiveness_rating,
            timeliness_rating,
            communication_rating
        ")
        ->where('user_id', $record->id)
        ->get();

        $breakdown = [
            'responsiveness' => ['Very Dissatisfied' => 0, 'Dissatisfied' => 0, 'Satisfied' => 0, 'Very Satisfied' => 0],
            'timeliness'     => ['Very Dissatisfied' => 0, 'Dissatisfied' => 0, 'Satisfied' => 0, 'Very Satisfied' => 0],
            'communication'  => ['Very Dissatisfied' => 0, 'Dissatisfied' => 0, 'Satisfied' => 0, 'Very Satisfied' => 0],
            'total'          => $results->count(),
        ];

        foreach ($results as $survey) {
            if (isset($breakdown['responsiveness'][$survey->responsiveness_rating])) {
                $breakdown['responsiveness'][$survey->responsiveness_rating]++;
            }
            if (isset($breakdown['timeliness'][$survey->timeliness_rating])) {
                $breakdown['timeliness'][$survey->timeliness_rating]++;
            }
            if (isset($breakdown['communication'][$survey->communication_rating])) {
                $breakdown['communication'][$survey->communication_rating]++;
            }
        }

        $categories = ['responsiveness', 'timeliness', 'communication'];
        $ratings = ['Very Dissatisfied', 'Dissatisfied', 'Satisfied', 'Very Satisfied'];
        $totalCounts = [];

        foreach ($ratings as $rating) {
            $totalCounts[$rating] = 0;
            foreach ($categories as $category) {
                $totalCounts[$rating] += $breakdown[$category][$rating];
            }
        }
        $breakdown['total_counts'] = $totalCounts;

        return $breakdown;
    }

    public function table(Table $table): Table
    {
        // load once when rendering table
        $this->loadRatingCounts();

        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('name')
                    ->label('Staff Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('surveys_count')
                    ->label('Total Surveys')
                    ->counts('surveys'),

                TextColumn::make('very_dissatisfied_count')
                    ->label('Very Dissatisfied')
                    ->getStateUsing(fn (User $record) => $this->ratingCounts[$record->id]['very_dissatisfied_count'] ?? 0),

                TextColumn::make('dissatisfied_count')
                    ->label('Dissatisfied')
                    ->getStateUsing(fn (User $record) => $this->ratingCounts[$record->id]['dissatisfied_count'] ?? 0),

                TextColumn::make('satisfied_count')
                    ->label('Satisfied')
                    ->getStateUsing(fn (User $record) => $this->ratingCounts[$record->id]['satisfied_count'] ?? 0),

                TextColumn::make('very_satisfied_count')
                    ->label('Very Satisfied')
                    ->getStateUsing(fn (User $record) => $this->ratingCounts[$record->id]['very_satisfied_count'] ?? 0),
            ])
            ->actions([
                Action::make('view_details')
                    ->label('View Details')
                    ->modalHeading('')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->icon('heroicon-o-eye')
                    ->modalContent(function (User $record) {
                        $counts = $this->getDetailedRatingCountsForRecord($record);
                        return view('components.survey-details-modal', [
                            'record' => $record,
                            'counts' => $counts,
                        ]);
                    })
            ]);
    }

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null; // Return null if you are not using Filament's translatable content features
    }

    public function render()
    {
        return view('livewire.survey-rate-counter');
    }
}
