<?php

use Webkul\Partner\Filament\Resources\AddressResource;
use Webkul\Partner\Filament\Resources\BankAccountResource;
use Webkul\Partner\Filament\Resources\BankResource;
use Webkul\Partner\Filament\Resources\IndustryResource;
use Webkul\Partner\Filament\Resources\PartnerResource;
use Webkul\Partner\Filament\Resources\TagResource;
use Webkul\Partner\Filament\Resources\TitleResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            BankAccountResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            AddressResource::class     => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            BankResource::class        => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            IndustryResource::class    => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            PartnerResource::class     => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            TagResource::class         => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            TitleResource::class       => [...$basic, ...$delete],
        ],
        'exclude' => [],
    ],
];
