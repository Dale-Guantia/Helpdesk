<?php

namespace App\Livewire;

use App\Models\User;
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

class UserActivity extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user && ($user->isSuperAdmin() || $user->isDepartmentHead() || $user->isDivisionHead());
    }

    protected function getTableQuery()
    {
        $user = Auth::user();

        $query = User::query()
            ->with(['department', 'office']); // eager load relationships

        // Restrict by office unless SuperAdmin
        if ($user->isSuperAdmin()) {

        }
        elseif ($user->isDepartmentHead()) {
            $query->where('department_id', $user->department_id);
        }
        else{
            $query->where('office_id', $user->office_id);
        }

        // Exclude users with role = 4 and only include department ID 1
        $query->where('role', '!=', USER::ROLE_EMPLOYEE)
            ->where('department_id', 1); // assuming 'department_id' is the correct column

        return $query;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery()) // Example query
            ->columns([
                TextColumn::make('id')
                    ->label('User ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        return $record->id === auth()->id() ? 'You' : $state;
                    }),
                // TextColumn::make('department.department_name')->label('Department')
                //     ->extraAttributes(['class' => 'text-xs'])
                //     ->searchable()
                //     ->limit(20)
                //     ->sortable(),
                TextColumn::make('office.office_name')->label('Division')
                    ->default('N/A')
                    ->searchable()
                    ->limit(20)
                    ->sortable(),
                TextColumn::make('resolved_tickets_count')->label('Total Resolved Tickets')
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
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null; // Return null if you are not using Filament's translatable content features
    }

    public function render()
    {
        return view('livewire.user-activity');
    }
}
