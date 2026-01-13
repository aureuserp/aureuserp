<?php

return [
    'resources' => [
        'manage' => [
            \Webkul\Security\Filament\Resources\TeamResource::class => ['view_any', 'view', 'create', 'update', 'delete'],
            \Webkul\Security\Filament\Resources\UserResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
        ],
        'exclude' => [],
    ],

];
