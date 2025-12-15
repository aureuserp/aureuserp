<?php

return [
    'navigation' => [
        'title' => 'Products',
        'group' => 'Inventory',
    ],

    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'Inventory',

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Tracking',

                        'fields' => [
                            'track-inventory'              => 'Track Inventory',
                            'track-inventory-hint-tooltip' => 'A storable product is one that requires inventory management..',
                            'track-by'                     => 'Track By',
                            'expiration-date'              => 'Expiration Date',
                            'expiration-date-hint-tooltip' => 'If selected, you can specify expiration dates for the product and its associated lot/serial numbers.',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Operations',

                        'fields' => [
                            'routes'              => 'Routes',
                            'routes-hint-tooltip' => 'Based on the installed modules, this setting allows you to define the product\'s route, such as purchasing, manufacturing, or replenishing on order.',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Logistics',

                        'fields' => [
                            'responsible'              => 'Responsible',
                            'responsible-hint-tooltip' => 'Delivery lead time (in days) represents the promised duration between sales order confirmation and product delivery.',
                            'weight'                   => 'Weight',
                            'volume'                   => 'Volume',
                            'sale-delay'               => 'Customer Lead Time (Days)',
                            'sale-delay-hint-tooltip'  => 'Delivery lead time (in days) represents the promised duration between sales order confirmation and product delivery.',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Traceability',

                        'fields' => [
                            'expiration-date'               => 'Expiration Date (Days)',
                            'expiration-date-hint-tooltip'  => 'If selected, you can set expiration dates for the product and its associated lot/serial numbers.',
                            'best-before-date'              => 'Best Before Date (Days)',
                            'best-before-date-hint-tooltip' => 'The number of days before the expiration date when the product begins to deteriorate, though it is still safe to use. This is calculated based on the lot/serial number.',
                            'removal-date'                  => 'Removal Date (Days)',
                            'removal-date-hint-tooltip'     => 'The number of days before the expiration date when the product should be removed from stock. This is calculated based on the lot/serial number.',
                            'alert-date'                    => 'Alert Date (Days)',
                            'alert-date-hint-tooltip'       => 'The number of days before the expiration date when an alert should be triggered for the lot/serial number. This is calculated based on the lot/serial number.',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Additional',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'inventory' => [
                'title' => 'Inventory',

                'entries' => [
                ],

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Tracking',

                        'entries' => [
                            'track-inventory' => 'Track Inventory',
                            'track-by'        => 'Track By',
                            'expiration-date' => 'Expiration Date',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Operations',

                        'entries' => [
                            'routes' => 'Routes',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Logistics',

                        'entries' => [
                            'responsible' => 'Responsible',
                            'weight'      => 'Weight',
                            'volume'      => 'Volume',
                            'sale-delay'  => 'Customer Lead Time (Days)',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Traceability',

                        'entries' => [
                            'expiration-date'  => 'Expiration Date (Days)',
                            'best-before-date' => 'Best Before Date (Days)',
                            'removal-date'     => 'Removal Date (Days)',
                            'alert-date'       => 'Alert Date (Days)',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
