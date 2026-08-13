<?php

namespace Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource\Pages;

use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource\Pages\EditTimeOff as BaseEditTimeOff;
use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource;

class EditByEmployee extends BaseEditTimeOff
{
    protected static string $resource = ByEmployeeResource::class;
}
