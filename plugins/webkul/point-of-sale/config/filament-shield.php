<?php

use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\BillResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\CategoryResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\FloorResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\NoteResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\PaymentMethodResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PaymentResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PrinterResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\PluginSettings;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Reporting;
use Webkul\PointOfSale\Filament\Admin\Clusters\Reporting\Pages\OrderReport;
use Webkul\PointOfSale\Filament\Admin\Clusters\Reporting\Pages\SalesDetails;
use Webkul\PointOfSale\Filament\Admin\Clusters\Reporting\Pages\SessionReport;
use Webkul\PointOfSale\Filament\Admin\Clusters\Settings\Pages\ManageAccounts;
use Webkul\PointOfSale\Filament\Admin\Clusters\Settings\Pages\ManageInventory;
use Webkul\PointOfSale\Filament\Admin\Clusters\Settings\Pages\ManageProducts;
use Webkul\PointOfSale\Filament\Admin\Pages\Dashboard;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            ConfigResource::class        => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            PaymentMethodResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            SessionResource::class       => [...$basic, ...$delete],
            OrderResource::class         => [...$basic, ...$delete],
            CategoryResource::class      => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            FloorResource::class         => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            BillResource::class          => [...$basic, ...$delete, ...$reorder],
            NoteResource::class          => [...$basic, ...$delete, ...$reorder],
            PrinterResource::class       => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            PaymentResource::class       => [...$basic],
            CustomerResource::class      => [...$basic, ...$delete],
            ProductResource::class       => [...$basic, ...$delete, ...$restore, ...$forceDelete],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'manage' => [
            Dashboard::class,
            ManageAccounts::class,
            ManageInventory::class,
            ManageProducts::class,
            OrderReport::class,
            SalesDetails::class,
            SessionReport::class,
        ],

        'exclude' => [
            Configurations::class,
            Orders::class,
            PluginSettings::class,
            Products::class,
            Reporting::class,
        ],
    ],
];
