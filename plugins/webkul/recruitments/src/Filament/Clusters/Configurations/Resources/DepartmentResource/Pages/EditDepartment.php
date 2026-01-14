<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DepartmentResource\Pages;

use Webkul\Employee\Filament\Resources\DepartmentResource\Pages\EditDepartment as BaseEditDepartment;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DepartmentResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditDepartment extends BaseEditDepartment
{
    use HasRecordNavigationTabs;

    protected static string $resource = DepartmentResource::class;
}
