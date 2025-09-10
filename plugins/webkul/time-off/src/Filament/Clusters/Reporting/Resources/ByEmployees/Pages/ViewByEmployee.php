<?php

namespace Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployees\Pages;

use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployees\ByEmployeeResource;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffs\Pages\ViewTimeOff as BaseViewTimeOff;

class ViewByEmployee extends BaseViewTimeOff
{
    protected static string $resource = ByEmployeeResource::class;
}
