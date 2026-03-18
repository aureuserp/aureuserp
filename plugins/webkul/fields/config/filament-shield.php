<?php

use Webkul\Field\Filament\Resources\FieldResource;

$permissions = [
    'FULL' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any', 'reorder'],
];

return [
    'resources' => [
        'manage' => [
            FieldResource::class => $permissions['FULL'],
        ],
        'exclude' => [],
    ],

];
