<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Resources\UserResource;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserActivities extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = UserResource::class;

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string $view = 'filament.pages.user_activities';

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

        return $user && ($user->isSuperAdmin() || $user->isHrdoDivisionHead());
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
            Tables\Columns\TextColumn::make('office.office_name')->label('Division Name')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('resolved_tickets_count')->label('Total Resolved Tickets')
                ->sortable(),
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
