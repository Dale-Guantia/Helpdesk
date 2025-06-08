<?php

namespace App\Filament\Pages;


use Filament\Pages\Page;
use App\Filament\Resources\UserResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;


class Reports extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?int $navigationSort = 8;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string $view = 'filament.pages.user_activities';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Download report')
                ->icon('heroicon-m-arrow-down-tray')
                ->url(route('reports_pdf')) // your PDF route here
                ->openUrlInNewTab()
                ->button(),
        ];
    }

    public function getDefaultTableSortColumn(): ?string
    {
        return 'resolved_tickets_count'; // your column name
    }

    public function getDefaultTableSortDirection(): ?string
    {
        return 'desc'; // sort highest to lowest
    }

    protected function getTableQuery()
    {
        $user = Auth::user();

        $query = User::query()->with(['office']); // make sure office relationship is loaded

        // Restrict by office unless SuperAdmin
        if (!$user->isSuperAdmin()) {
            $query->where('office_id', $user->office_id);
        }

        // Exclude users with role_id = 4 (employee)
        $query->where('role', '!=', 4);

        return $query;
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user && ($user->isSuperAdmin() || $user->isDivisionHead());
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id')
                ->label('User ID')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->formatStateUsing(function ($state, $record) {
                    return $record->id === auth()->id() ? 'You' : $state;
                }),
            Tables\Columns\TextColumn::make('department.department_name')->label('Department')
                ->extraAttributes(['class' => 'text-xs'])
                ->searchable()
                ->limit(20)
                ->sortable(),
            Tables\Columns\TextColumn::make('office.office_name')->label('Division')
                ->default('N/A')
                ->searchable()
                ->limit(20)
                ->sortable(),
            Tables\Columns\TextColumn::make('resolved_tickets_count')->label('Total Resolved Tickets')
                ->sortable(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('department_id')
                ->label('Department')
                ->multiple()
                ->relationship('department', 'department_name'),

            Tables\Filters\SelectFilter::make('office_id')
                ->label('Division')
                ->multiple()
                ->relationship('office', 'office_name'),
        ];
    }

    protected function getTableActions(): array
    {
        // No edit/view/delete actions
        return [];
    }

    protected function getTableBulkActions(): array
    {
        // No bulk delete/export
        return [];
    }
}
