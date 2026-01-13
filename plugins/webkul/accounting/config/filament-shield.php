<?php

return [
    'resources' => [
        'manage' => [],
        'exclude' => [
            \Webkul\Accounting\Filament\Clusters\Vendors\Resources\ProductResource::class,
            \Webkul\Accounting\Filament\Clusters\Vendors\Resources\PaymentResource::class,
        ],
    ],

    'pages' => [
        'exclude' => [
            \Webkul\Accounting\Filament\Clusters\Vendors::class,
            \Webkul\Accounting\Filament\Clusters\Customer::class,
            \Webkul\Accounting\Filament\Clusters\Accounting::class,
            \Webkul\Accounting\Filament\Clusters\Configuration::class,
        ],
    ],

    'widgets' => [
        'exclude' => [
            \Webkul\Accounting\Filament\Widgets\JournalChartsWidget::class,
        ],
    ],

];
