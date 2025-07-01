<?php

namespace App\Filament\Pages\Auth;

use DiogoGPinto\AuthUIEnhancer\Pages\Auth\Concerns\HasCustomLayout;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use App\Models\Department;
use App\Models\Office;

class Register extends BaseRegister
{
    use HasCustomLayout;

    public function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        // $this->getPhoneComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        // $this->getDepartmentComponent(),
                        // $this->getOfficeComponent(),

                    ])
                    ->statePath('data'),
            ),
        ];
    }

    // protected function getPhoneComponent(): Component
    // {
    //     return TextInput::make('phone')
    //         ->tel()
    //         ->maxLength(11)
    //         ->minLength(10);
    // }

    // protected function getDepartmentComponent(): Component
    // {
    //     return Select::make('department_id')
    //         ->label('Department')
    //         ->reactive()
    //         ->options(fn () => Department::pluck('department_name', 'id')->toArray());
    // }
    // protected function getOfficeComponent(): Component
    // {
    //     return Select::make('office_id')
    //         ->label('Division')
    //         ->reactive()
    //         ->disabled(fn (callable $get) => !$get('department_id'))
    //         ->options(function (callable $get) {
    //             $department_id = $get('department_id');

    //             if (!$department_id) {
    //                 return [];
    //             }
    //             return Office::where('department_id', $department_id)
    //                 ->pluck('office_name', 'id')
    //                 ->toArray();
    //         });
    // }
}
