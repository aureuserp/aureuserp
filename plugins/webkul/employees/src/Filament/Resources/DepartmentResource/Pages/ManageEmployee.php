<?php

namespace Webkul\Employee\Filament\Resources\DepartmentResource\Pages;

use BackedEnum;
use Filament\Resources\Pages\ManageRelatedRecords;
use Webkul\Employee\Filament\Resources\DepartmentResource;
use Webkul\Employee\Traits\Resources\Department\DepartmentEmployee;

class ManageEmployee extends ManageRelatedRecords
{
    use DepartmentEmployee;

    protected static string $resource = DepartmentResource::class;

    protected static string $relationship = 'employees';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/resources/department/pages/manage-employee.navigation.title');
    }
}
