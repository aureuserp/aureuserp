<?php

use Webkul\Contact\Filament\Clusters\Configurations;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\BankAccountResource;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\BankResource;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\IndustryResource;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\TagResource;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\TitleResource;
use Webkul\Contact\Filament\Resources\PartnerResource;

return [
    'resources' => [
        'manage' => [
            PartnerResource::class                             => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            TagResource::class                                 => ['view_any', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            TitleResource::class                               => ['view_any', 'create', 'update', 'delete', 'delete_any'],
            IndustryResource::class                            => ['view_any', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            BankAccountResource::class                         => ['view_any', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            BankResource::class                                => ['view_any', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            Configurations::class,
        ],
    ],

];
