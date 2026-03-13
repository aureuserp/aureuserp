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

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            EmployeeResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            DepartmentResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            EmployeeSkillResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            ActivityPlanResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            CalendarResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            DepartureReasonResource::class => [...$basic, ...$delete, ...$reorder],
            EmployeeCategoryResource::class => [...$basic, ...$delete],
            WorkLocationResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            SkillTypeResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            EmploymentTypeResource::class => [...$basic, ...$delete, ...$reorder],
            JobPositionResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
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
