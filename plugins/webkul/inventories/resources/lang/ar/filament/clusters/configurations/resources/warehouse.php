<?php

return [
    'navigation' => [
        'title' => 'Warehouses',
        'group' => 'Warehouse Management',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'name'               => 'Name',
                    'name-placeholder'   => 'eg. Central Warehouse',
                    'code'               => 'Short Name',
                    'code-placeholder'   => 'eg. CW',
                    'code-hint-tooltip'  => 'The short name serves as an identifier for the warehouse.',
                    'company'            => 'Company',
                    'address'            => 'Address',
                ],
            ],

            'settings' => [
                'title'  => 'Settings',

                'fields' => [
                    'shipment-management'              => 'Shipment Management',
                    'incoming-shipments'               => 'Incoming Shipments',
                    'incoming-shipments-hint-tooltip'  => 'Default incoming route to follow',
                    'outgoing-shipments'               => 'Outgoing Shipments',
                    'outgoing-shipments-hint-tooltip'  => 'Default outgoing route to follow',
                    'resupply-management'              => 'Resupply Management',
                    'resupply-management-hint-tooltip' => 'Routes will be automatically generated to resupply this warehouse from the selected warehouses.',
                    'resupply-from'                    => 'Resupply From',
                ],
            ],

            'additional' => [
                'title'  => 'Additional Information',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Name',
            'code'       => 'Short Name',
            'company'    => 'Company',
            'address'    => 'Address',
            'deleted-at' => 'deleted At',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'groups' => [
            'address'       => 'Address',
            'company'       => 'Company',
            'created-at'    => 'Created At',
            'updated-at'    => 'Updated At',
        ],

        'filters' => [
            'company' => 'Company',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Warehouse restored',
                    'body'  => 'The warehouse has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Warehouse deleted',
                    'body'  => 'The warehouse has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Warehouse force deleted',
                        'body'  => 'The warehouse has been force deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Warehouse could not be deleted',
                        'body'  => 'The warehouse cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Warehouses restored',
                    'body'  => 'The warehouses has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Warehouses deleted',
                    'body'  => 'The warehouses has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Warehouses force deleted',
                        'body'  => 'The warehouses has been force deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Warehouses could not be deleted',
                        'body'  => 'The warehouses cannot be deleted because they are currently in use.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General Information',

                'entries' => [
                    'name'    => 'Warehouse Name',
                    'code'    => 'Warehouse Code',
                    'company' => 'Company',
                    'address' => 'Address',
                ],
            ],

            'settings' => [
                'title' => 'Settings',

                'entries' => [
                    'shipment-management' => 'Shipment Management',
                    'incoming-shipments'  => 'Incoming Shipments',
                    'outgoing-shipments'  => 'Outgoing Shipments',
                    'resupply-management' => 'Resupply Management',
                    'resupply-from'       => 'Resupply From',
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
