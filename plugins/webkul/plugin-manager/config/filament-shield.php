<?php

use Webkul\PluginManager\Filament\Resources\PluginResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            PluginResource::class => [...$basic, ...$delete, ...$reorder],
        ],
        'exclude' => [],
    ],
];
