<?php

return [
    'resources' => [
        'manage' => [
            \Webkul\Employee\Filament\Resources\EmployeeResource::class                                 => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any'],
            \Webkul\Employee\Filament\Resources\DepartmentResource::class                               => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Employee\Filament\Clusters\Reportings\Resources\EmployeeSkillResource::class        => ['view_any', 'view'],
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\ActivityPlanResource::class     => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource::class         => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\DepartureReasonResource::class  => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\EmployeeCategoryResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\WorkLocationResource::class     => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource::class        => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Employee\Filament\Clusters\Configurations\Resources\EmploymentTypeResource::class   => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            \Webkul\Employee\Filament\Clusters\Configurations::class,
            \Webkul\Employee\Filament\Clusters\Reportings::class,
        ],
    ],

];
