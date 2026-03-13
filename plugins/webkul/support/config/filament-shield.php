<?php

use Webkul\Support\Filament\Resources\ActivityTypeResource;
use Webkul\Support\Filament\Resources\BankResource;
use Webkul\Support\Filament\Resources\CompanyResource;
use Webkul\Support\Filament\Resources\CountryResource;
use Webkul\Support\Filament\Resources\CurrencyResource;
use Webkul\Support\Filament\Resources\StateResource;
use Webkul\Support\Filament\Resources\UOMCategoryResource;

$permissions = [
    'BASIC'       => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
    'REORDER'     => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
    'SOFT_DELETE' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any'],
    'FULL'        => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any', 'reorder'],
];

return [
    'resources' => [
        'manage' => [
            ActivityTypeResource::class => $permissions['FULL'],
            BankResource::class         => $permissions['SOFT_DELETE'],
            CompanyResource::class      => $permissions['FULL'],
            CountryResource::class      => $permissions['BASIC'],
            CurrencyResource::class     => $permissions['BASIC'],
            StateResource::class        => $permissions['BASIC'],
            UOMCategoryResource::class  => $permissions['BASIC'],
        ],
        'exclude' => [],
    ],
];
