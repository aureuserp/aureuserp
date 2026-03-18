<?php

use Webkul\Contact\Filament\Clusters\Configurations;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\BankAccountResource;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\BankResource;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\IndustryResource;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\TagResource;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\TitleResource;
use Webkul\Contact\Filament\Resources\AddressResource;
use Webkul\Contact\Filament\Resources\PartnerResource;

$permissions = [
    'BASIC' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
    'SOFT_DELETE' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any'],
];

return [
    'resources' => [
        'manage' => [
            PartnerResource::class => $permissions['SOFT_DELETE'],
            TagResource::class => $permissions['SOFT_DELETE'],
            TitleResource::class => $permissions['BASIC'],
            IndustryResource::class => $permissions['SOFT_DELETE'],
            BankAccountResource::class => $permissions['SOFT_DELETE'],
            BankResource::class => $permissions['SOFT_DELETE'],
            AddressResource::class => $permissions['SOFT_DELETE'],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            Configurations::class,
        ],
    ],

];
