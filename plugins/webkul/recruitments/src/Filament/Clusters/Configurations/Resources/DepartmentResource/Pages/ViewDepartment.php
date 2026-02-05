<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DepartmentResource\Pages;

use Webkul\Employee\Filament\Resources\DepartmentResource\Pages\ViewDepartment as BaseViewDepartment;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DepartmentResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewDepartment extends BaseViewDepartment
{
    use HasRecordNavigationTabs;

    protected static string $resource = DepartmentResource::class;
}
