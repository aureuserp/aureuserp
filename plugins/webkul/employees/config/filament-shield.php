<?php

use Webkul\Employee\Filament\Clusters\Configurations;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\ActivityPlanResource;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\DepartureReasonResource;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\EmployeeCategoryResource;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\EmploymentTypeResource;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositionResource;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\WorkLocationResource;
use Webkul\Employee\Filament\Clusters\Reportings;
use Webkul\Employee\Filament\Clusters\Reportings\Resources\EmployeeSkillResource;
use Webkul\Employee\Filament\Resources\DepartmentResource;
use Webkul\Employee\Filament\Resources\EmployeeResource;

$permissions = [
    'BASIC' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
    'REORDER' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
    'SOFT_DELETE' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any'],
    'FULL' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any', 'reorder'],
];

return [
    'resources' => [
        'manage' => [
            EmployeeResource::class => $permissions['SOFT_DELETE'],
            DepartmentResource::class => $permissions['SOFT_DELETE'],
            EmployeeSkillResource::class => $permissions['SOFT_DELETE'],
            ActivityPlanResource::class => $permissions['SOFT_DELETE'],
            CalendarResource::class => $permissions['SOFT_DELETE'],
            DepartureReasonResource::class => $permissions['REORDER'],
            EmployeeCategoryResource::class => $permissions['BASIC'],
            WorkLocationResource::class => $permissions['SOFT_DELETE'],
            SkillTypeResource::class => $permissions['SOFT_DELETE'],
            EmploymentTypeResource::class => $permissions['REORDER'],
            JobPositionResource::class => $permissions['FULL'],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            Configurations::class,
            Reportings::class,
        ],
    ],

];
