<?php

use Webkul\Partner\Filament\Resources\AddressResource;
use Webkul\Partner\Filament\Resources\BankAccountResource;
use Webkul\Partner\Filament\Resources\BankResource;
use Webkul\Partner\Filament\Resources\IndustryResource;
use Webkul\Partner\Filament\Resources\PartnerResource;
use Webkul\Partner\Filament\Resources\TagResource;
use Webkul\Partner\Filament\Resources\TitleResource;

$permissions = [
    'BASIC' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
    'SOFT_DELETE' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any'],
];

return [
    'resources' => [
        'manage' => [
            BankAccountResource::class => $permissions['SOFT_DELETE'],
            AddressResource::class => $permissions['SOFT_DELETE'],
            BankResource::class => $permissions['SOFT_DELETE'],
            IndustryResource::class => $permissions['SOFT_DELETE'],
            PartnerResource::class => $permissions['SOFT_DELETE'],
            TagResource::class => $permissions['SOFT_DELETE'],
            TitleResource::class => $permissions['BASIC'],
        ],
        'exclude' => [],
    ],
];
