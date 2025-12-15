<?php

return [
    'navigation' => [
        'title' => 'Scraps',
        'group' => 'Adjustments',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'product'              => 'Product',
                    'package'              => 'Package',
                    'quantity'             => 'Quantity',
                    'unit'                 => 'Unit of Measure',
                    'lot'                  => 'Lot/Serial',
                    'tags'                 => 'Tags',
                    'name'                 => 'Name',
                    'color'                => 'Color',
                    'owner'                => 'Owner',
                    'source-location'      => 'Source Location',
                    'destination-location' => 'Scrap Location',
                    'source-document'      => 'Source Document',
                    'company'              => 'Company',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'date'            => 'Date',
            'reference'       => 'Reference',
            'product'         => 'Product',
            'package'         => 'Package',
            'quantity'        => 'Quantity',
            'uom'             => 'Unit of Measure',
            'source-location' => 'Source Location',
            'scrap-location'  => 'Scrap Location',
            'unit'            => 'Unit of Measure',
            'lot'             => 'Lot/Serial',
            'tags'            => 'Tags',
            'state'           => 'State',
        ],

        'groups' => [
            'product'              => 'Product',
            'source-location'      => 'Source Location',
            'destination-location' => 'Scrap Location',
        ],

        'filters' => [
            'source-location'      => 'Source Location',
            'destination-location' => 'Scrap Location',
            'product'              => 'Product',
            'state'                => 'State',
            'product-category'     => 'Product Category',
            'uom'                  => 'Unit of Measure',
            'lot'                  => 'Lot/Serial',
            'package'              => 'Package',
            'tags'                 => 'Tags',
            'company'              => 'Company',
            'quantity'             => 'Quantity',
            'creator'              => 'Creator',
            'closed-at'            => 'Closed At',
            'created-at'           => 'Created At',
            'updated-at'           => 'Updated At',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Scrap deleted',
                        'body'  => 'The scrap has been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Scrap could not be deleted',
                        'body'  => 'The scrap cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Scraps deleted',
                        'body'  => 'The selected scraps have been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Scraps could not be deleted',
                        'body'  => 'The scraps cannot be deleted because they are currently in use.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Scrap Details',

                'entries' => [
                    'product'              => 'Product',
                    'quantity'             => 'Quantity',
                    'lot'                  => 'Lot',
                    'tags'                 => 'Tags',
                    'package'              => 'Package',
                    'owner'                => 'Owner',
                    'source-location'      => 'Source Location',
                    'destination-location' => 'Destination Location',
                    'source-document'      => 'Source Document',
                    'company'              => 'Company',
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
