<?php

return [
    'resources' => [
        'manage' => [
            \Webkul\Invoice\Filament\Clusters\Customer\Resources\CustomerResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Invoice\Filament\Clusters\Customer\Resources\PaymentResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNoteResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Invoice\Filament\Clusters\Customer\Resources\InvoiceResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\BankAccountResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\IncoTermResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTermResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributeResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroupResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
        ],
        'exclude' => [
            \Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource::class,
            \Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentResource::class,
        ],
    ],

    'pages' => [
        'exclude' => [
            \Webkul\Invoice\Filament\Clusters\Vendors::class,
            \Webkul\Invoice\Filament\Clusters\Customer::class,
            \Webkul\Invoice\Filament\Clusters\Configuration::class,
        ],
    ],

];
