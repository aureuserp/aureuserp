<?php

use Webkul\TimeOff\Filament\Clusters\Configurations;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\ActivityTypeResource;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypeResource;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\MandatoryDayResource;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\PublicHolidayResource;
use Webkul\TimeOff\Filament\Clusters\Management;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\AllocationResource;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource;
use Webkul\TimeOff\Filament\Clusters\MyTime;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocationResource;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource;
use Webkul\TimeOff\Filament\Clusters\Overview;
use Webkul\TimeOff\Filament\Clusters\Reporting;
use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource;

$permissions = [
    'BASIC' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
    'FULL' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any', 'reorder'],
];

return [
    'resources' => [
        'manage' => [
            MyTimeOffResource::class => $permissions['BASIC'],
            MyAllocationResource::class => $permissions['BASIC'],
            AllocationResource::class => $permissions['BASIC'],
            TimeOffResource::class => $permissions['BASIC'],
            ByEmployeeResource::class => $permissions['BASIC'],
            AccrualPlanResource::class => $permissions['BASIC'],
            PublicHolidayResource::class => $permissions['BASIC'],
            MandatoryDayResource::class => $permissions['BASIC'],
            LeaveTypeResource::class => $permissions['FULL'],
            ActivityTypeResource::class => $permissions['FULL'],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            Configurations::class,
            Management::class,
            MyTime::class,
            Overview::class,
            Reporting::class,
        ],
    ],

];
