<?php

return [
    'navigation' => [
        'title' => 'Routes',
        'group' => 'Warehouse Management',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'route'             => 'Route',
                    'route-placeholder' => 'eg. Two Step Reception',
                    'company'           => 'Company',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Applicable On',
                'description' => 'Choose the locations where this route can be applied.',

                'fields' => [
                    'products'                        => 'Products',
                    'products-hint-tooltip'           => 'If selected, this route will be available for selection on the product.',
                    'product-categories'              => 'Product Categories',
                    'product-categories-hint-tooltip' => 'If selected, this route will be available for selection on the product category.',
                    'warehouses'                      => 'Warehouses',
                    'warehouses-hint-tooltip'         => 'When a warehouse is assigned to this route, it will be considered the default route for products moving through that warehouse.',
                    'packaging'                       => 'Packaging',
                    'packaging-hint-tooltip'          => 'If selected, this route will be available for selection on the packaging.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'route'      => 'Route',
            'company'    => 'Company',
            'deleted-at' => 'Deleted At',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'groups' => [
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'filters' => [
            'company'  => 'Company',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Route updated',
                    'body'  => 'The route has been updated successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Route restored',
                    'body'  => 'The route has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Route deleted',
                    'body'  => 'The route has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Route force deleted',
                        'body'  => 'The route has been force deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Route could not be deleted',
                        'body'  => 'The route cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Routes restored',
                    'body'  => 'The routes has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Routes deleted',
                    'body'  => 'The routes has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Routes force deleted',
                        'body'  => 'The routes has been force deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Routes could not be deleted',
                        'body'  => 'The routes cannot be deleted because they are currently in use.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'entries' => [
                    'route'             => 'Route',
                    'route-placeholder' => 'eg. Two Step Reception',
                    'company'           => 'Company',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Applicable On',
                'description' => 'Select the places where this route can be selected.',

                'entries' => [
                    'products'                        => 'Products',
                    'products-hint-tooltip'           => 'If selected, this route will be available for selection on the product.',
                    'product-categories'              => 'Product Categories',
                    'product-categories-hint-tooltip' => 'If selected, this route will be available for selection on the product category.',
                    'warehouses'                      => 'Warehouses',
                    'warehouses-hint-tooltip'         => 'When a warehouse is assigned to this route, it will be considered the default route for products moving through that warehouse.',
                    'packaging'                       => 'Packaging',
                    'packaging-hint-tooltip'          => 'If selected, this route will be available for selection on the packaging.',
                ],
            ],

            'record-information' => [
                'title' => 'Record Information',

                'entries' => [
                    'created-by'   => 'Created By',
                    'created-at'   => 'Created At',
                    'last-updated' => 'Last Updated',
                ],
            ],
        ],
    ],
];
