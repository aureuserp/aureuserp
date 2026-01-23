<?php

use Webkul\Support\Filament\Resources\ActivityTypeResource;
use Webkul\Support\Filament\Resources\CurrencyResource;

return [
    'resources' => [
        'manage'  => [],
        'exclude' => [
            CurrencyResource::class,
            ActivityTypeResource::class,
        ],
    ],

];
