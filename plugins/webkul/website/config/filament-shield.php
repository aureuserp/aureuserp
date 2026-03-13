<?php

use Webkul\Website\Filament\Admin\Clusters\Configurations;
use Webkul\Website\Filament\Admin\Resources\PageResource;
use Webkul\Website\Filament\Admin\Resources\PartnerResource;
use Webkul\Website\Filament\Customer\Resources\PageResource as WebsitePageResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            PageResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            PartnerResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            WebsitePageResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            Configurations::class,
        ],
    ],
];
