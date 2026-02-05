<?php

use Webkul\Field\Filament\Resources\FieldResource;

return [
    'resources' => [
        'manage' => [
            FieldResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
        ],
        'exclude' => [],
    ],

];
