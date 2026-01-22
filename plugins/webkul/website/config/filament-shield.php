<?php

return [
    'resources' => [
        'manage' => [
            \Webkul\Website\Filament\Admin\Resources\PageResource::class    => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Website\Filament\Admin\Resources\PartnerResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            \Webkul\Website\Filament\Admin\Clusters\Configurations::class,
        ],
    ],

];
