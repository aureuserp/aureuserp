<?php

namespace Webkul\Employee\Filament\Resources\EmployeeResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Employee\Traits\Resources\Employee\EmployeeSkillRelation;

class ManageSkill extends ManageRelatedRecords
{
    use EmployeeSkillRelation;

    protected static string $resource = EmployeeResource::class;

    protected static string $relationship = 'skills';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/resources/employee/pages/manage-skill.navigation.title');
    }
}
