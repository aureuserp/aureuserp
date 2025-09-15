<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositionResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositionResource;

class ViewJobPosition extends ViewRecord
{
    protected static string $resource = JobPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
