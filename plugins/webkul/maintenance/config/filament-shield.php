<?php

use Webkul\Maintenance\Filament\Clusters\Configurations;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\StageResource;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Maintenance\Filament\Clusters\Maintenance;
use Webkul\Maintenance\Filament\Clusters\Maintenance\Pages\Calendar;
use Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource;
use Webkul\Maintenance\Filament\Resources\EquipmentResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            EquipmentCategoryResource::class  => [...$basic, ...$delete],
            EquipmentResource::class          => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            MaintenanceRequestResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            StageResource::class              => [...$basic, ...$delete, ...$reorder],
            TeamResource::class               => [...$basic, ...$delete, ...$restore, ...$forceDelete],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'manage' => [
            Calendar::class => ['view'],
        ],
        'exclude' => [
            Configurations::class,
            Maintenance::class,
        ],
    ],
];
