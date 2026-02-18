<?php

use Webkul\Security\Filament\Resources\TeamResource;
use Webkul\Security\Filament\Resources\UserResource;

return [
    'resources' => [
        'manage' => [
            TeamResource::class => ['view_any', 'view', 'create', 'update', 'delete'],
            UserResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
        ],
        'exclude' => [],
    ],

];
