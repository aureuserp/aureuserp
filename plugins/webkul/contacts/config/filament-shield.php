<?php

return [
    'resources' => [
        'manage' => [
            \Webkul\Contact\Filament\Resources\PartnerResource::class                             => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Contact\Filament\Clusters\Configurations\Resources\TagResource::class         => ['view_any', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Contact\Filament\Clusters\Configurations\Resources\TitleResource::class       => ['view_any', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Contact\Filament\Clusters\Configurations\Resources\IndustryResource::class    => ['view_any', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Contact\Filament\Clusters\Configurations\Resources\BankAccountResource::class => ['view_any', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Contact\Filament\Clusters\Configurations\Resources\BankResource::class        => ['view_any', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            \Webkul\Contact\Filament\Clusters\Configurations::class,
        ],
    ],

];
