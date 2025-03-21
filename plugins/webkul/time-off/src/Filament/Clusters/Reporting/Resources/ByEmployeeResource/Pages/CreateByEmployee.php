<?php

namespace Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource;

class CreateByEmployee extends CreateRecord
{
    protected static string $resource = ByEmployeeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
