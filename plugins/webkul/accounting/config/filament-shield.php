<?php

use Webkul\Accounting\Filament\Clusters\Accounting;
use Webkul\Accounting\Filament\Clusters\Configuration;
use Webkul\Accounting\Filament\Clusters\Customers;
use Webkul\Accounting\Filament\Clusters\Vendors;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\PaymentResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\ProductResource;
use Webkul\Accounting\Filament\Widgets\JournalChartsWidget;

return [
    'resources' => [
        'manage'  => [],
        'exclude' => [
            ProductResource::class,
            PaymentResource::class,
        ],
    ],

    'pages' => [
        'exclude' => [
            Vendors::class,
            Customers::class,
            Accounting::class,
            Configuration::class,
        ],
    ],

    'widgets' => [
        'exclude' => [
            JournalChartsWidget::class,
        ],
    ],

];
