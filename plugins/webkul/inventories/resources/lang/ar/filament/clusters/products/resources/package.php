<?php

return [
    'navigation' => [
        'title' => 'Packages',
        'group' => 'Inventory',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'             => 'Name',
                    'name-placeholder' => 'eg. PACK007',
                    'package-type'     => 'Package Type',
                    'pack-date'        => 'Pack Date',
                    'location'         => 'Location',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Name',
            'package-type' => 'Package Type',
            'location'     => 'Location',
            'company'      => 'Company',
            'created-at'   => 'Created At',
            'updated-at'   => 'Updated At',
        ],

        'groups' => [
            'package-type'   => 'Package Type',
            'location'       => 'Location',
            'created-at'     => 'Created At',
        ],

        'filters' => [
            'package-type' => 'Package Type',
            'location'     => 'Location',
            'creator'      => 'Creator',
            'company'      => 'Company',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Package deleted',
                        'body'  => 'The package has been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Package could not be deleted',
                        'body'  => 'The package cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print-without-content' => [
                'label' => 'Print Barcode',
            ],

            'print-with-content' => [
                'label' => 'Print Barcode With Content',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Packages deleted',
                        'body'  => 'The packages has been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Packages could not be deleted',
                        'body'  => 'The packages cannot be deleted because they are currently in use.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Package Details',

                'entries' => [
                    'name'         => 'Package Name',
                    'package-type' => 'Package Type',
                    'pack-date'    => 'Pack Date',
                    'location'     => 'Location',
                    'company'      => 'Company',
                    'created-at'   => 'Created At',
                    'updated-at'   => 'Last Updated',
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
