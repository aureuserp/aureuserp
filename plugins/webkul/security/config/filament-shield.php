<?php

use Webkul\Security\Filament\Resources\CompanyResource;
use Webkul\Security\Filament\Resources\RoleResource;
use Webkul\Security\Filament\Resources\TeamResource;
use Webkul\Security\Filament\Resources\UserResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            TeamResource::class => [...$basic, ...$delete],
            UserResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            CompanyResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            RoleResource::class => [...$basic, ...$delete],
        ],
        'exclude' => [],
    ],
];
