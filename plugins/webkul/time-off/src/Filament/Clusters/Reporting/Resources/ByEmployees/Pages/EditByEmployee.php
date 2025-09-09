<?php

namespace Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployees\Pages;

use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployees\ByEmployeeResource;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffs\Pages\EditTimeOff as BaseEditTimeOff;

class EditByEmployee extends BaseEditTimeOff
{
    protected static string $resource = ByEmployeeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
