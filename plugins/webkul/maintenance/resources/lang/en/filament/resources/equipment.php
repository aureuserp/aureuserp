<?php

return [
    'navigation' => [
        'title' => 'Equipment',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General Information',
                'fields' => [
                    'name' => 'Name',
                    'note' => 'Description',
                ],
            ],

            'settings' => [
                'title'  => 'Settings',
                'fields' => [
                    'category'   => 'Equipment Category',
                    'team'       => 'Maintenance Team',
                    'company'    => 'Company',
                    'technician' => 'Technician',
                    'owner'      => 'Owner',
                    'location'   => 'Used in location',
                ],
            ],

            'product-information' => [
                'title'  => 'Product Information',
                'fields' => [
                    'partner'                     => 'Vendor',
                    'partner-ref'                 => 'Vendor Reference',
                    'model'                       => 'Model',
                    'serial-no'                   => 'Serial Number',
                    'effective-date'              => 'Effective Date',
                    'effective-date-hint-tooltip' => 'Used as the starting point for calculating Mean Time Between Failure.',
                    'cost'                        => 'Cost',
                    'warranty-date'               => 'Warranty Expiration Date',
                ],
            ],

            'maintenance' => [
                'title'  => 'Maintenance',
                'fields' => [
                    'expected-mtbf' => 'Expected Mean Time Between Failure',
                ],
                'suffixes' => [
                    'days' => 'days',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Equipment Name',
            'owner'      => 'Owner',
            'serial-no'  => 'Serial Number',
            'category'   => 'Equipment Category',
            'technician' => 'Technician',
            'company'    => 'Company',
            'created-at' => 'Created At',
        ],

        'filters' => [
            'category'   => 'Equipment Category',
            'team'       => 'Maintenance Team',
            'technician' => 'Technician',
        ],

        'groups' => [
            'category'   => 'Equipment Category',
            'owner'      => 'Owner',
            'technician' => 'Technician',
            'vendor'     => 'Vendor',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Equipment updated',
                    'body'  => 'The equipment has been updated successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Equipment restored',
                    'body'  => 'The equipment has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipment archived',
                    'body'  => 'The equipment has been archived successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Equipment deleted',
                        'body'  => 'The equipment has been permanently deleted.',
                    ],

                    'error' => [
                        'title' => 'Equipment could not be deleted',
                        'body'  => 'This equipment is referenced by another record.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Equipment restored',
                    'body'  => 'The selected equipment has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipment archived',
                    'body'  => 'The selected equipment has been archived successfully.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Equipment created',
                    'body'  => 'The equipment has been created successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'General Information',
                'entries' => [
                    'name' => 'Name',
                    'note' => 'Description',
                ],
            ],

            'settings' => [
                'title'   => 'Settings',
                'entries' => [
                    'category'   => 'Equipment Category',
                    'team'       => 'Maintenance Team',
                    'company'    => 'Company',
                    'technician' => 'Technician',
                    'owner'      => 'Owner',
                    'location'   => 'Used in location',
                ],
            ],

            'product-information' => [
                'title'   => 'Product Information',
                'entries' => [
                    'partner'        => 'Vendor',
                    'partner-ref'    => 'Vendor Reference',
                    'model'          => 'Model',
                    'serial-no'      => 'Serial Number',
                    'effective-date' => 'Effective Date',
                    'cost'           => 'Cost',
                    'warranty-date'  => 'Warranty Expiration Date',
                ],
            ],

            'maintenance' => [
                'title'   => 'Maintenance',
                'entries' => [
                    'expected-mtbf'          => 'Expected Mean Time Between Failure',
                    'maintenance-count'      => 'Maintenance Count',
                    'maintenance-open-count' => 'Open Maintenance Count',
                    'assigned-at'            => 'Assigned Date',
                    'scraped-at'             => 'Scrap Date',
                ],
                'suffixes' => [
                    'days' => 'days',
                ],
            ],
        ],
    ],
];
