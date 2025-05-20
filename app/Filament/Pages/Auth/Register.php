<?php

namespace App\Filament\Pages\Auth;

use DiogoGPinto\AuthUIEnhancer\Pages\Auth\Concerns\HasCustomLayout;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
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
                        $this->getOfficeComponent(),

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

    protected function getOfficeComponent(): Component
    {
        return Select::make('office_id')
            ->options(fn () => Office::pluck('office_name', 'id')->toArray())
            ->required();
    }
}
