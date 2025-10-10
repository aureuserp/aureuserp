<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'             => 'Name',
                    'name-placeholder' => 'eg. T-shirt',
                    'description'      => 'Description',
                    'tags'             => 'Tags',
                ],
            ],

            'images' => [
                'title' => 'Images',
            ],

            'inventory' => [
                'title' => 'Inventory',

                'fields' => [],

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Logistics',

                        'fields' => [
                            'weight' => 'Weight',
                            'volume' => 'Volume',
                        ],
                    ],
                ],
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
            'favorite'        => 'Favorite',
            'name'            => 'Name',
            'variants'        => 'Variants',
            'images'          => 'Images',
            'type'            => 'Type',
            'reference'       => 'Reference',
            'responsible'     => 'Responsible',
            'barcode'         => 'Barcode',
            'category'        => 'Category',
            'company'         => 'Company',
            'price'           => 'Price',
            'cost'            => 'Cost',
            'on-hand'         => 'On Hand',
            'tags'            => 'Tags',
            'deleted-at'      => 'Deleted At',
            'created-at'      => 'Created At',
            'updated-at'      => 'Updated At',
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
                    'success' => [
                        'title' => 'Product force deleted',
                        'body'  => 'The product has been force deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Product could not be deleted',
                        'body'  => 'The product cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Print Labels',

                'form' => [
                    'fields' => [
                        'quantity' => 'Number of Labels',
                        'format'   => 'Format',

                        'format-options' => [
                            'dymo'       => 'Dymo',
                            '2x7_price'  => '2x7 with price',
                            '4x7_price'  => '4x7 with price',
                            '4x12'       => '4x12',
                            '4x12_price' => '4x12 with price',
                        ],
                    ],
                ],
            ],

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
                    'success' => [
                        'title' => 'Products force deleted',
                        'body'  => 'The products has been force deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Products could not be deleted',
                        'body'  => 'The products cannot be deleted because they are currently in use.',
                    ],
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

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Logistics',

                        'entries' => [
                            'weight' => 'Weight',
                            'volume' => 'Volume',
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
