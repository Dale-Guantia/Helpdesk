<?php

namespace App\Filament\Resources\PriorityResource\Pages;

use App\Filament\Resources\PriorityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePriority extends CreateRecord
{
    protected static string $resource = PriorityResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
