<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\Departments\Pages;

use Webkul\Employee\Filament\Resources\Departments\Pages\CreateDepartment as BaseCreateDepartment;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\Departments\DepartmentResource;

class CreateDepartment extends BaseCreateDepartment
{
    protected static string $resource = DepartmentResource::class;
}
