<?php

namespace Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource\Pages;

use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource\Pages\ViewTimeOff as BaseViewTimeOff;
use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource;

class ViewByEmployee extends BaseViewTimeOff
{
    protected static string $resource = ByEmployeeResource::class;
}
