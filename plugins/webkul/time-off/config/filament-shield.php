<?php

return [
    'resources' => [
        'manage' => [
            \Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocationResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\TimeOff\Filament\Clusters\Management\Resources\AllocationResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\TimeOff\Filament\Clusters\Configurations\Resources\PublicHolidayResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\TimeOff\Filament\Clusters\Configurations\Resources\MandatoryDayResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypeResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            \Webkul\TimeOff\Filament\Clusters\Configurations::class,
            \Webkul\TimeOff\Filament\Clusters\Management::class,
            \Webkul\TimeOff\Filament\Clusters\MyTime::class,
            \Webkul\TimeOff\Filament\Clusters\Overview::class,
            \Webkul\TimeOff\Filament\Clusters\Reporting::class,
        ],
    ],

];
