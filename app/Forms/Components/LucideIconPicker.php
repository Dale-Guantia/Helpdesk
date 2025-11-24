<?php

namespace App\Forms\Components;

use App\Support\LucideIcons;
use Filament\Forms\Components\Field;

class LucideIconPicker extends Field
{
    protected string $view = 'forms.components.lucide-icon-picker';

    public function getIcons(): array
    {
        return LucideIcons::all();
    }
}
