<?php

namespace Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource\Pages;

use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource\Pages\ListTimeOffs;
use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource;

class ListByEmployees extends ListTimeOffs
{
    protected static string $resource = ByEmployeeResource::class;
}
