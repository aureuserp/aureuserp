<?php

namespace Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployees\Pages;

use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployees\ByEmployeeResource;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffs\Pages\ListTimeOff;

class ListByEmployees extends ListTimeOff
{
    protected static string $resource = ByEmployeeResource::class;
}
