<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Office;
use App\Models\ProblemCategory;
use Illuminate\Support\Facades\DB;
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

class TicketOverview extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && ($user->isSuperAdmin() || $user->isDivisionHead());
    }

    protected function getTableQuery(): Builder
    {
        $query = ProblemCategory::query()
            ->withCount('tickets');

        $user = Auth::user();

        if ($user && $user->isDivisionHead()) {

            $query->whereHas('office', function (Builder $officeQuery) use ($user) {
                $officeQuery->where('id', $user->office_id);
            });

        } else {

            $query->whereHas('office', function (Builder $officeQuery) {
                $officeQuery->where('department_id', 1);
            });
        }

        return $query;
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();

        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('category_name')
                    ->label('Issue description')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tickets_count')
                    ->label('Total Tickets')
                    ->sortable(),
                TextColumn::make('average_resolve_time')
                    ->label('Average Resolve Time per Ticket')
                    ->getStateUsing(function (ProblemCategory $record): string {
                        $seconds = Ticket::where('problem_category_id', $record->id)
                            ->whereNotNull('resolved_at')
                            ->avg(DB::raw('TIMESTAMPDIFF(SECOND, created_at, resolved_at)'));

                        if ($seconds) {
                            $hours = floor($seconds / 3600);
                            $minutes = floor(($seconds % 3600) / 60);
                            return "{$hours}h {$minutes}m";
                        }
                        return 'N/A';
                    })
                    ->sortable(false),
            ])
            ->filters([
                SelectFilter::make('office_id') // Name matches the public property for default value
                    ->label('Division')
                    ->multiple(false) // Changed to false, as you mentioned "selected division" (singular)
                    ->options(
                        // Only show offices from department_id 1 in the filter dropdown
                        Office::where('department_id', 1)->pluck('office_name', 'id')->toArray()
                    )
                    ->query(function (Builder $query, array $data): Builder {
                        // This closure defines how the filter applies to the main query
                        if (isset($data['value']) && filled($data['value'])) {
                            $query->whereHas('office', function (Builder $officeQuery) use ($data) {
                                $officeQuery->where('id', $data['value']);
                            });
                        }
                        return $query;
                    })
                    ->visible(fn () => $user && $user->isSuperAdmin()), // <-- HIDE FILTER IF NOT SUPERADMIN
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

    protected function getForms(): array
    {
        return [];
    }

    public function render()
    {
        // These can be fetched here if needed for other sections of the view
        $userActivities = User::with(['department', 'office'])
            ->where('department_id', 1)
            ->where('role', '!=', 4)
            ->get();

        $divisions = Office::where('department_id', 1)
            ->with(['problemCategories' => function ($query) {
                $query->withCount('tickets');
            }])
            ->get();

        foreach ($divisions as $division) {
            foreach ($division->problemCategories as $category) {
                $seconds = Ticket::where('problem_category_id', $category->id)
                    ->whereNotNull('resolved_at')
                    ->avg(DB::raw('TIMESTAMPDIFF(SECOND, created_at, resolved_at)'));

                if ($seconds) {
                    $hours = floor($seconds / 3600);
                    $minutes = floor(($seconds % 3600) / 60);
                    $category->average_resolve_time = "{$hours}h {$minutes}m";
                } else {
                    $category->average_resolve_time = 'N/A';
                }
            }
        }

        return view('livewire.ticket-overview', [
            'userActivities' => $userActivities,
            'divisionsData' => $divisions,
        ]);
    }
}
