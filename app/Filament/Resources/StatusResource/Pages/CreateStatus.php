<?php

namespace App\Filament\Resources\StatusResource\Pages;

use App\Filament\Resources\StatusResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStatus extends CreateRecord
{
    protected static string $resource = StatusResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
