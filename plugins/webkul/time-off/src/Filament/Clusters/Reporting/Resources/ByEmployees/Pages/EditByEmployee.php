<?php

namespace Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployees\Pages;

use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource\Pages\EditTimeOff as BaseEditTimeOff;
use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployees\ByEmployeeResource;

class EditByEmployee extends BaseEditTimeOff
{
    protected static string $resource = ByEmployeeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
