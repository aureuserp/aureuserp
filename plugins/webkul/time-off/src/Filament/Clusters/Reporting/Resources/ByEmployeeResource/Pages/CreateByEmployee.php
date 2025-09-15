<?php

namespace Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource\Pages\CreateTimeOff as BaseCreateTimeOff;

class CreateByEmployee extends BaseCreateTimeOff
{
    protected static string $resource = ByEmployeeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = parent::mutateFormDataBeforeCreate($data);

        $data['date_from'] = $data['request_date_from'] ?? null;
        $data['date_to'] = $data['request_date_to'] ?? null;

        return $data;
    }
}
