<?php

return [
    'title' => 'Barcode',

    'navigation' => [
        'back'        => 'Back',
        'home'        => 'Operations',
        'search'      => 'Search...',
        'label'       => 'Navigation',
        'open'        => 'Open navigation',
        'coming-soon' => 'Coming soon',
    ],

    'auth' => [
        'login-title'       => 'Barcode Login',
        'login-heading'     => 'Sign in to Barcode',
        'login-subheading'  => 'Continue to the barcode operations app.',
    ],

    'filament' => [
        'navigation' => [
            'group' => 'Barcode',
            'label' => 'Barcode App',
        ],
    ],

    'dashboard' => [
        'operations' => 'Operations',
        'empty'      => 'No operations available.',
    ],

    'operation-search' => [
        'placeholder'    => 'Scan or enter operation barcode...',
        'open'           => 'Open',
        'not-found'      => 'No active operation found for this barcode.',
        'multiple-found' => ':count matching operations found.',
    ],

    'transfers' => [
        'title' => 'Transfers',
        'empty' => 'No transfers found.',
    ],

    'adjustments' => [
        'title'             => 'Inventory Adjustments',
        'subtitle'          => 'Count stock by location, product, or lot',
        'search'            => 'Scan or search by location, product, lot, serial...',
        'empty'             => 'No inventory quantities found.',
        'location-scanned'  => 'Scanning location :location. Scan more products here or scan another location.',
        'location-cleared'  => 'Inventory adjustment filters cleared.',
        'product-not-found' => 'This product is not available in the current inventory selection.',
        'lot-not-found'     => 'This lot or serial is not available in the current inventory selection.',
        'multiple-found'    => ':count matching inventory quantities found.',
        'count-saved'       => 'Inventory count saved.',
        'count-applied'     => 'Inventory adjustment applied.',
        'count-cleared'     => 'Inventory count cleared.',
        'counted'           => 'Counted',
        'on-hand'           => 'On hand',
        'location'          => 'Location',
        'product'           => 'Product',
        'lot-serial'        => 'Lot/Serial',
        'clear-filters'     => 'Clear filters',
        'apply'             => 'Apply',
        'clear'             => 'Clear',
        'editor-title'      => 'Adjustment details',
        'editor-subtitle'   => 'Review stock details and update the counted quantity.',
        'editor-image'      => 'Inventory quantity image',
        'edit-tooltip'      => 'Edit inventory quantity',
    ],

    'operation' => [
        'scan'                 => 'Scan a product, lot, package, packaging, or transfer',
        'manual-scan'          => 'Scan or search by product, reference, barcode...',
        'search'               => 'Search product, reference, barcode...',
        'moves'                => 'Moves',
        'source'               => 'From',
        'available'            => 'Available',
        'discard'              => 'Discard',
        'confirm'              => 'Confirm',
        'counted'              => 'Counted',
        'lot-serial'           => 'Lot/Serial Number',
        'stock-title'          => 'Quantity in Stock',
        'empty-moves'          => 'No moves found.',
        'details-title'        => 'Move details',
        'settings-title'       => 'Move settings',
        'pick-from'            => 'Pick From',
        'destination-location' => 'Destination Location',
        'destination-package'  => 'Destination Package',
        'select-package'       => 'Select package',
        'stock-subtitle'       => 'Select where else to pick the product from',
        'no-stock-locations'   => 'No stock locations found.',
        'camera-unavailable'   => 'Camera unavailable',
        'submit-scan'          => 'Submit scan',
        'image-alt'            => 'Move line image',
        'edit-tooltip'         => 'Edit move line',
    ],

    'scan' => [
        'empty'                    => 'Enter or scan a barcode.',
        'not-found'                => 'No matching barcode found.',
        'operation-matched'        => 'Transfer matched.',
        'product-not-on-operation' => 'This product is not part of the operation.',
        'package-matched'          => 'Package matched.',
        'move-located'             => 'Move located. Enter the counted quantity.',
        'move-updated'             => 'Move quantity updated.',
        'move-counted'             => 'Move marked as counted.',
    ],

    'actions' => [
        'confirm'                  => 'Confirm',
        'confirm-prompt'           => 'Are you sure you want to',
        'cancel'                   => 'Cancel',
        'check-availability'       => 'Check Availability',
        'validate'                 => 'Validate',
        'return'                   => 'Return',
        'stay-on-transfer'         => 'Discard',
        'no-backorder'             => 'No Backorder',
        'backorder-title'          => 'Incomplete Transfer',
        'backorder-prompt'         => 'If you validate now, the remaining products will be added to a backorder.',
        'backorder-col-product'    => 'Product',
        'backorder-col-done-todo'  => 'Done / To Do',
        'backorder-col-backorder'  => 'Backorder',
        'completed'                => 'Action completed.',
        'unsupported'              => 'Unsupported barcode action.',
        'no-moves'                 => 'This operation has no moves.',
        'no-return-quantities'     => 'There are no quantities to return.',
    ],
];
