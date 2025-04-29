<?php

namespace App\Filament\Resources\ProblemCategoryResource\Pages;

use App\Filament\Resources\ProblemCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProblemCategory extends EditRecord
{
    protected static string $resource = ProblemCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
