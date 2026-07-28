<?php

use Webkul\Support\Filament\Resources\ActivityTypeResource;
use Webkul\Support\Filament\Resources\BankResource;
use Webkul\Support\Filament\Resources\CalendarResource;
use Webkul\Support\Filament\Resources\CompanyResource;
use Webkul\Support\Filament\Resources\CountryResource;
use Webkul\Support\Filament\Resources\CurrencyResource;
use Webkul\Support\Filament\Resources\StateResource;
use Webkul\Support\Filament\Resources\UOMCategoryResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            ActivityTypeResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            CalendarResource::class     => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            BankResource::class         => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            CompanyResource::class      => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            CountryResource::class      => [...$basic, ...$delete],
            CurrencyResource::class     => [...$basic, ...$delete],
            StateResource::class        => [...$basic, ...$delete],
            UOMCategoryResource::class  => [...$basic, ...$delete],
        ],
        'exclude' => [],
    ],
];
