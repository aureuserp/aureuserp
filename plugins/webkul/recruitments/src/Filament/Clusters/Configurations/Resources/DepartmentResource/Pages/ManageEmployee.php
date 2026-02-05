<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DepartmentResource\Pages;

use Webkul\Employee\Filament\Resources\DepartmentResource\Pages\ManageEmployee as BaseManageEmployee;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DepartmentResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageEmployee extends BaseManageEmployee
{
    use HasRecordNavigationTabs;

    protected static string $resource = DepartmentResource::class;
}
