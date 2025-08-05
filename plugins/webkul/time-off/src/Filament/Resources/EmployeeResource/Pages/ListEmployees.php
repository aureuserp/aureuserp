<?php

namespace Webkul\TimeOff\Filament\Resources\EmployeeResource\Pages;

use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ListEmployees as BaseListEmployees;
use Webkul\TimeOff\Filament\Resources\EmployeeResource;

class ListEmployees extends BaseListEmployees
{
    protected static string $resource = EmployeeResource::class;
}
