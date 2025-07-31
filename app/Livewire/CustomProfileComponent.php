<?php

namespace App\Livewire;

use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;
use App\Models\Department;
use App\Models\Office;

class CustomProfileComponent extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort;

    public ?array $data = [];

    protected static int $sort = 0;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        $user = auth()->user();

        // Initialize variables for department and office names
        $departmentName = null;
        $officeName = null;

        // Check if the user exists and has a department_id
        if ($user && $user->department_id) {
            // Find the department by ID and get its name
            $department = Department::find($user->department_id);
            if ($department) {
                $departmentName = $department->department_name;
            }
        }

        // Check if the user exists and has an office_id
        if ($user && $user->office_id) {
            // Find the office by ID and get its name
            $office = Office::find($user->office_id);
            if ($office) {
                $officeName = $office->office_name;
            }
        }

        $hideOfficeAssignment = false;
        if ($user) {
            if (method_exists($user, 'isEmployee') && $user->isEmployee()) {
                $hideOfficeAssignment = true;
            }
        }

        return $form
            ->schema([
                Section::make('Office and Role Assignment')
                    ->aside()
                    ->description('Your department,division, and role assignment.')
                    ->schema([
                        Forms\Components\TextInput::make('department_id')
                            ->label('Department')
                            ->default($departmentName)
                            ->disabled(),
                        Forms\Components\TextInput::make('office_name')
                            ->label('Division / Office')
                            ->default($officeName)
                            ->disabled(),
                        Forms\Components\TextInput::make('role_name')
                            ->label('User Role')
                            ->default(auth()->user()?->role_name ?? 'N/A')
                            ->disabled(),
                    ])
                    ->hidden($hideOfficeAssignment),
            ])
            ->statePath('data');
    }

    // public function save(): void
    // {
    //     $data = $this->form->getState();
    // }

    public function render(): View
    {
        return view('livewire.custom-profile-component');
    }
}
