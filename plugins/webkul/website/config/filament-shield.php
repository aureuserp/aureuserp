<?php

use Webkul\Website\Filament\Admin\Clusters\Configurations;
use Webkul\Website\Filament\Admin\Resources\PageResource;
use Webkul\Website\Filament\Admin\Resources\PartnerResource;
use Webkul\Website\Filament\Customer\Resources\PageResource as WebsitePageResource;

$permissions = [
    'SOFT_DELETE' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any'],
];

return [
    'resources' => [
        'manage' => [
            PageResource::class => $permissions['SOFT_DELETE'],
            PartnerResource::class => $permissions['SOFT_DELETE'],
            WebsitePageResource::class => $permissions['SOFT_DELETE'],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            Configurations::class,
        ],
    ],

];
