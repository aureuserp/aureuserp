<?php

namespace Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployees\Pages;

use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource\Pages\ListTimeOff;
use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployees\ByEmployeeResource;

class ListByEmployees extends ListTimeOff
{
    protected static string $resource = ByEmployeeResource::class;
}
