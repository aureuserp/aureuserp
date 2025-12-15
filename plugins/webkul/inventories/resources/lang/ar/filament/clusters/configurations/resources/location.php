<?php

return [
    'navigation' => [
        'title' => 'Locations',
        'group' => 'Warehouse Management',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'location'                     => 'Location',
                    'location-placeholder'         => 'eg. Spare Stock',
                    'parent-location'              => 'Parent Location',
                    'parent-location-hint-tooltip' => 'The main location that encompasses this location. For example, the \'Dispatch Zone\' is part of the \'Gate 1\' parent location.',
                    'external-notes'               => 'External Notes',
                ],
            ],

            'settings' => [
                'title'  => 'Settings',

                'fields' => [
                    'location-type'                 => 'Location Type',
                    'company'                       => 'Company',
                    'storage-category'              => 'Storage Category',
                    'is-scrap'                      => 'Is a Scrap Location?',
                    'is-scrap-hint-tooltip'         => 'Select this checkbox to designate this location for storing scrapped or damaged goods.',
                    'is-dock'                       => 'Is a Dock Location?',
                    'is-dock-hint-tooltip'          => 'Select this checkbox to designate this location for storing goods that are ready for shipment.',
                    'is-replenish'                  => 'Is a Replenish Location?',
                    'is-replenish-hint-tooltip'     => 'Enable this function to retrieve all quantities needed for replenishment at this location.',
                    'logistics'                     => 'Logistics',
                    'removal-strategy'              => 'Removal Strategy',
                    'removal-strategy-hint-tooltip' => 'Specifies the default method for determining the exact shelf, lot, and location from which to pick products. This method can be enforced at the product category level, with a fallback to parent locations if not set here.',
                    'cyclic-counting'               => 'Cyclic Counting',
                    'inventory-frequency'           => 'Inventory Frequency',
                    'last-inventory'                => 'Last Inventory',
                    'last-inventory-hint-tooltip'   => 'Date of the last inventory at this location.',
                    'next-expected'                 => 'Next expected',
                    'next-expected-hint-tooltip'    => 'Date for next planned inventory based on cyclic schedule.',
                ],
            ],

            'additional' => [
                'title'  => 'Additional Information',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'location'         => 'Location',
            'type'             => 'Type',
            'storage-category' => 'Storage Category',
            'company'          => 'Company',
            'deleted-at'       => 'deleted At',
            'created-at'       => 'Created At',
            'updated-at'       => 'Updated At',
        ],

        'groups' => [
            'warehouse'       => 'Warehouse',
            'type'            => 'Type',
            'created-at'      => 'Created At',
            'updated-at'      => 'Updated At',
        ],

        'filters' => [
            'location' => 'Location',
            'type'     => 'Type',
            'company'  => 'Company',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Location updated',
                    'body'  => 'The location has been updated successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Location restored',
                    'body'  => 'The location has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Location deleted',
                    'body'  => 'The location has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Location force deleted',
                        'body'  => 'The location has been force deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Location could not be deleted',
                        'body'  => 'The location cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Print Barcode',
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Locations restored',
                    'body'  => 'The locations has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Locations deleted',
                    'body'  => 'The locations has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Locations force deleted',
                        'body'  => 'The locations has been force deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Locations could not be deleted',
                        'body'  => 'The locations cannot be deleted because they are currently in use.',
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
                    'location'                     => 'Location',
                    'location-placeholder'         => 'eg. Spare Stock',
                    'parent-location'              => 'Parent Location',
                    'parent-location-hint-tooltip' => 'The main location that encompasses this location. For example, the \'Dispatch Zone\' is part of the \'Gate 1\' parent location.',
                    'external-notes'               => 'External Notes',
                ],
            ],

            'settings' => [
                'title'  => 'Settings',

                'entries' => [
                    'location-type'                 => 'Location Type',
                    'company'                       => 'Company',
                    'storage-category'              => 'Storage Category',
                    'is-scrap'                      => 'Is a Scrap Location?',
                    'is-scrap-hint-tooltip'         => 'Select this checkbox to designate this location for storing scrapped or damaged goods.',
                    'is-dock'                       => 'Is a Dock Location?',
                    'is-dock-hint-tooltip'          => 'Select this checkbox to designate this location for storing goods that are ready for shipment.',
                    'is-replenish'                  => 'Is a Replenish Location?',
                    'is-replenish-hint-tooltip'     => 'Enable this function to retrieve all quantities needed for replenishment at this location.',
                    'logistics'                     => 'Logistics',
                    'removal-strategy'              => 'Removal Strategy',
                    'removal-strategy-hint-tooltip' => 'Specifies the default method for determining the exact shelf, lot, and location from which to pick products. This method can be enforced at the product category level, with a fallback to parent locations if not set here.',
                    'cyclic-counting'               => 'Cyclic Counting',
                    'inventory-frequency'           => 'Inventory Frequency',
                    'last-inventory'                => 'Last Inventory',
                    'last-inventory-hint-tooltip'   => 'Date of the last inventory at this location.',
                    'next-expected'                 => 'Next expected',
                    'next-expected-hint-tooltip'    => 'Date for next planned inventory based on cyclic schedule.',
                ],
            ],

            'additional' => [
                'title'  => 'Additional Information',
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
