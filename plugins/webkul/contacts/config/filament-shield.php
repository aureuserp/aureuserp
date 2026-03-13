<?php

use Webkul\Contact\Filament\Clusters\Configurations;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\BankAccountResource;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\BankResource;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\IndustryResource;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\TagResource;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\TitleResource;
use Webkul\Contact\Filament\Resources\AddressResource;
use Webkul\Contact\Filament\Resources\PartnerResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            PartnerResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            TagResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            TitleResource::class => [...$basic, ...$delete],
            IndustryResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            BankAccountResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            BankResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            AddressResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            Configurations::class,
        ],
    ],
];
