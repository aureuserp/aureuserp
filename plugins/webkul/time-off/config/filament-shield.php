<?php

use Webkul\TimeOff\Filament\Clusters\Configurations;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource;
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

return [
    'resources' => [
        'manage' => [
            MyTimeOffResource::class             => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            MyAllocationResource::class          => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            AllocationResource::class            => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            TimeOffResource::class               => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            ByEmployeeResource::class            => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            AccrualPlanResource::class           => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            PublicHolidayResource::class         => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            MandatoryDayResource::class          => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            LeaveTypeResource::class             => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
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
