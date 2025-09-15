<?php

namespace Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource\Pages;

use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource\Pages\ListTimeOff;
use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource;

class ListByEmployees extends ListTimeOff
{
    protected static string $resource = ByEmployeeResource::class;
}
