<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\Departments\Pages;

use Webkul\Employee\Filament\Resources\Departments\Pages\EditDepartment as BaseEditDepartment;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\Departments\DepartmentResource;

class EditDepartment extends BaseEditDepartment
{
    protected static string $resource = DepartmentResource::class;
}
