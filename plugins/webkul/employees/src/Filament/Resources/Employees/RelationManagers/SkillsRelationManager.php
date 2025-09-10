<?php

namespace Webkul\Employee\Filament\Resources\Employees\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Webkul\Employee\Traits\Resources\Employee\EmployeeSkillRelation;

class SkillsRelationManager extends RelationManager
{
    use EmployeeSkillRelation;

    protected static string $relationship = 'skills';

    protected static ?string $title = 'Skills';
}
