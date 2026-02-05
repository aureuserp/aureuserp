<?php

use Webkul\Employee\Filament\Clusters\Configurations;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\ActivityPlanResource;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\DepartureReasonResource;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\EmployeeCategoryResource;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\EmploymentTypeResource;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\WorkLocationResource;
use Webkul\Employee\Filament\Clusters\Reportings;
use Webkul\Employee\Filament\Clusters\Reportings\Resources\EmployeeSkillResource;
use Webkul\Employee\Filament\Resources\DepartmentResource;
use Webkul\Employee\Filament\Resources\EmployeeResource;

return [
    'resources' => [
        'manage' => [
            EmployeeResource::class                                 => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any'],
            DepartmentResource::class                               => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            EmployeeSkillResource::class                            => ['view_any', 'view'],
            ActivityPlanResource::class                             => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            CalendarResource::class                                 => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            DepartureReasonResource::class                          => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            EmployeeCategoryResource::class                         => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            WorkLocationResource::class                             => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            SkillTypeResource::class                                => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            EmploymentTypeResource::class                           => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
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
