<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\Departments\Pages;

use Webkul\Employee\Filament\Resources\Departments\Pages\ListDepartments as BaseListDepartments;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\Departments\DepartmentResource;

class ListDepartments extends BaseListDepartments
{
    protected static string $resource = DepartmentResource::class;
}
