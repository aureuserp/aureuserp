<?php

return [
    'resources' => [
        'manage' => [
            \Webkul\Partner\Filament\Resources\BankAccountResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
        ],
        'exclude' => [
            \Webkul\Partner\Filament\Resources\AddressResource::class,
            \Webkul\Partner\Filament\Resources\BankAccountResource::class,
            \Webkul\Partner\Filament\Resources\BankResource::class,
            \Webkul\Partner\Filament\Resources\IndustryResource::class,
            \Webkul\Partner\Filament\Resources\PartnerResource::class,
            \Webkul\Partner\Filament\Resources\TagResource::class,
            \Webkul\Partner\Filament\Resources\TitleResource::class,
        ],
    ],

];
