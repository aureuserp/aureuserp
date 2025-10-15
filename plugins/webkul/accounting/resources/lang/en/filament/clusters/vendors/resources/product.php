<?php

return [
    'navigation' => [
        'title' => 'Products',
        'group' => 'Inventory',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'             => 'Name',
                    'name-placeholder' => 'eg. T-shirt',
                    'description'      => 'Description',
                    'tags'             => 'Tags',
                    'sales'            => 'Sales',
                    'purchase'         => 'Purchase',
                ],
            ],

            'invoice-policy' => [
                'title'            => 'Invoice Policy',
                'ordered-policy'   => 'You can invoice goods before they are delivered.',
                'delivered-policy' => 'Invoice after delivery, based on quantities delivered, not ordered.',
            ],

            'images' => [
                'title' => 'Images',
            ],

            'settings' => [
                'title' => 'Settings',

                'fields' => [
                    'type'      => 'Type',
                    'reference' => 'Reference',
                    'barcode'   => 'Barcode',
                    'category'  => 'Category',
                    'company'   => 'Company',
                ],
            ],

            'category-and-tags' => [
                'title' => 'Category & Tags',

                'fields' => [
                    'category' => 'Category',
                    'tags'     => 'Tags',
                ],
            ],

            'pricing' => [
                'title' => 'Pricing',

                'fields' => [
                    'price' => 'Price',
                    'cost'  => 'Cost',
                ],
            ],

            'additional' => [
                'title' => 'Additional',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Name',
            'images'      => 'Images',
            'type'        => 'Type',
            'reference'   => 'Reference',
            'responsible' => 'Responsible',
            'barcode'     => 'Barcode',
            'category'    => 'Category',
            'company'     => 'Company',
            'price'       => 'Price',
            'cost'        => 'Cost',
            'tags'        => 'Tags',
            'deleted-at'  => 'Deleted At',
            'created-at'  => 'Created At',
            'updated-at'  => 'Updated At',
        ],

        'groups' => [
            'type'       => 'Type',
            'category'   => 'Category',
            'created-at' => 'Created At',
        ],

        'filters' => [
            'name'        => 'Name',
            'type'        => 'Type',
            'reference'   => 'Reference',
            'barcode'     => 'Barcode',
            'category'    => 'Category',
            'company'     => 'Company',
            'price'       => 'Price',
            'cost'        => 'Cost',
            'is-favorite' => 'Is Favorite',
            'weight'      => 'Weight',
            'volume'      => 'Volume',
            'tags'        => 'Tags',
            'responsible' => 'Responsible',
            'created-at'  => 'Created At',
            'updated-at'  => 'Updated At',
            'creator'     => 'Creator',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Product restored',
                    'body'  => 'The product has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Product deleted',
                    'body'  => 'The product has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Product force deleted',
                    'body'  => 'The product has been force deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Products restored',
                    'body'  => 'The products has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Products deleted',
                    'body'  => 'The products has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Products force deleted',
                    'body'  => 'The products has been force deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'name'             => 'Name',
                    'name-placeholder' => 'eg. T-shirt',
                    'description'      => 'Description',
                    'tags'             => 'Tags',
                ],
            ],

            'images' => [
                'title' => 'Images',

                'entries' => [],
            ],

            'settings' => [
                'title' => 'Settings',

                'entries' => [
                    'type'      => 'Type',
                    'reference' => 'Reference',
                    'barcode'   => 'Barcode',
                    'category'  => 'Category',
                    'company'   => 'Company',
                ],
            ],

            'pricing' => [
                'title' => 'Pricing',

                'entries' => [
                    'price' => 'Price',
                    'cost'  => 'Cost',
                ],
            ],

            'inventory' => [
                'title' => 'Inventory',

                'entries' => [],

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

            'record-information' => [
                'title' => 'Record Information',

                'entries' => [
                    'created-at' => 'Created At',
                    'created-by' => 'Created By',
                    'updated-at' => 'Updated At',
                ],
            ],
        ],
    ],
];
