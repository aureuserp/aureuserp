<?php

use Webkul\Partner\Filament\Resources\BankAccountResource;

return [
    'resources' => [
        'manage' => [
            BankAccountResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
        ],
        'exclude' => [
        ],
    ],
];
