<?php

namespace Webkul\Employee\Filament\Resources\Employees\Pages;

use BackedEnum;
use Filament\Resources\Pages\ManageRelatedRecords;
use Webkul\Employee\Filament\Resources\Employees\EmployeeResource;
use Webkul\Employee\Traits\Resources\Employee\EmployeeResumeRelation;

class ManageResume extends ManageRelatedRecords
{
    use EmployeeResumeRelation;

    protected static string $resource = EmployeeResource::class;

    protected static string $relationship = 'resumes';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/resources/employee/pages/manage-resume.navigation.title');
    }
}
