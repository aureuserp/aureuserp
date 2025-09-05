<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\Departments\Pages;

use Webkul\Employee\Filament\Resources\Departments\Pages\ViewDepartment as BaseViewDepartment;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\Departments\DepartmentResource;

class ViewDepartment extends BaseViewDepartment
{
    protected static string $resource = DepartmentResource::class;
}
