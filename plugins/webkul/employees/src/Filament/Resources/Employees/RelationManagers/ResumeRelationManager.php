<?php

namespace Webkul\Employee\Filament\Resources\Employees\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Webkul\Employee\Traits\Resources\Employee\EmployeeResumeRelation;

class ResumeRelationManager extends RelationManager
{
    use EmployeeResumeRelation;

    protected static string $relationship = 'resumes';

    protected static ?string $title = 'Resumes';
}
