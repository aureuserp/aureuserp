<?php

use Webkul\PluginManager\Filament\Resources\PluginResource;

$permissions = [
    'REORDER' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
];

return [
    'resources' => [
        'manage' => [
            PluginResource::class => $permissions['REORDER'],
        ],
        'exclude' => [],
    ],
];
