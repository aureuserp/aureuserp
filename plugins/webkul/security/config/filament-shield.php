<?php

use Webkul\Security\Filament\Resources\CompanyResource;
use Webkul\Security\Filament\Resources\RoleResource;
use Webkul\Security\Filament\Resources\TeamResource;
use Webkul\Security\Filament\Resources\UserResource;

$permissions = [
    'BASIC' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
    'REORDER' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
    'SOFT_DELETE' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any'],
    'FULL' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any', 'reorder'],
];

return [
    'resources' => [
        'manage' => [
            TeamResource::class => $permissions['BASIC'],
            UserResource::class => $permissions['SOFT_DELETE'],
            CompanyResource::class => $permissions['FULL'],
            RoleResource::class => $permissions['BASIC'],
        ],
        'exclude' => [],
    ],

];
