<?php

use Webkul\Website\Filament\Admin\Clusters\Configurations;
use Webkul\Website\Filament\Admin\Resources\PageResource;
use Webkul\Website\Filament\Admin\Resources\PartnerResource;

return [
    'resources' => [
        'manage' => [
            PageResource::class    => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            PartnerResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            Configurations::class,
        ],
    ],

];
