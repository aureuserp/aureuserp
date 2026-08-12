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

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            MyTimeOffResource::class     => [...$basic, ...$delete],
            MyAllocationResource::class  => [...$basic, ...$delete],
            AllocationResource::class    => [...$basic, ...$delete],
            TimeOffResource::class       => [...$basic, ...$delete],
            ByEmployeeResource::class    => [...$basic, ...$delete],
            AccrualPlanResource::class   => [...$basic, ...$delete],
            PublicHolidayResource::class => [...$basic, ...$delete],
            MandatoryDayResource::class  => [...$basic, ...$delete],
            LeaveTypeResource::class     => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            ActivityTypeResource::class  => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
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
