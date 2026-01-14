<?php

namespace Webkul\Security\Filament\Resources\RoleResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Security\Filament\Resources\RoleResource;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected function getActions(): array
    {
        return [
            EditAction::make()
                ->hidden(fn ($record) => $record->name == config('filament-shield.panel_user.name')),
        ];
    }
}
