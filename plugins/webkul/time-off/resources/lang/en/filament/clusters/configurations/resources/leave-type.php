<?php

return [
    'title'      => 'Leave Type',
    'navigation' => [
        'title' => 'Leave Type',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General Information',
                'fields' => [
                    'name'                => 'Title',
                    'approval'            => 'Approval',
                    'requires-allocation' => 'Requires Allocation',
                    'employee-requests'   => 'Employee Requests',
                    'display-option'      => 'Display Option',
                ],
            ],
            'display-option' => [
                'title'  => 'Display Option',
                'fields' => [
                    'color' => 'Color',
                ],
            ],
            'configuration' => [
                'title' => 'Configuration',

                'fields' => [
                    'notified-time-off-officers'          => 'Notified Time Off Officers',
                    'take-time-off-in'                    => 'Take Time Off In',
                    'public-holiday-included'             => 'Public Holiday Included',
                    'allow-to-attach-supporting-document' => 'Allow to Attach Supporting Document',
                    'show-on-dashboard'                   => 'Show on Dashboard',
                    'allow-negative-cap'                  => 'Allow Negative Cap',
                    'kind-off-time'                       => 'Kind of Time',
                    'max-negative-cap'                    => 'Max Negative Cap',
                    'kind-of-time'                        => 'Kind of Time Off',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                      => 'Name',
            'company-name'              => 'Company',
            'color'                     => 'Color',
            'notified-time-officers'    => 'Notified Time Officers',
            'time-off-approval'         => 'Time Off Approval',
            'requires-allocation'       => 'Requires Allocation',
            'allocation-approval'       => 'Allocation Approval',
            'employee-request'          => 'Employee Request',
        ],

        'filters' => [
            'name'                => 'Name',
            'company-name'        => 'Company',
            'time-off-approval'   => 'Time Off Approval',
            'requires-allocation' => 'Requires Allocation',
            'time-type'           => 'Time Type',
            'request-unit'        => 'Request Unit',
            'created-by'          => 'Created By',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Leave Type deleted',
                    'body'  => 'The Leave type has been deleted successfully.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Leave Type restored',
                    'body'  => 'The Leave type has been restored successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Leave Type restored',
                    'body'  => 'The Leave Type has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Leave Type deleted',
                    'body'  => 'The Leave Type has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Leave Type force deleted',
                        'body'  => 'The Leave Type has been force deleted successfully.',
                    ],
                    'error' => [
                        'title' => 'Leave Type could not be deleted',
                        'body'  => 'The Leave Type cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'General Information',
                'entries' => [
                    'name'                => 'Title',
                    'approval'            => 'Approval',
                    'requires-allocation' => 'Requires Allocation',
                    'employee-requests'   => 'Employee Requests',
                    'display-option'      => 'Display Option',
                ],
            ],
            'display-option' => [
                'title'   => 'Display Option',
                'entries' => [
                    'color' => 'Color',
                ],
            ],
            'configuration' => [
                'title' => 'Configuration',

                'entries' => [
                    'notified-time-off-officers'          => 'Notified Time Off Officers',
                    'take-time-off-in'                    => 'Take Time Off In',
                    'public-holiday-included'             => 'Public Holiday Included',
                    'allow-to-attach-supporting-document' => 'Allow to Attach Supporting Document',
                    'show-on-dashboard'                   => 'Show on Dashboard',
                    'kind-off-time'                       => 'Kind of Time',
                    'max-negative-cap'                    => 'Max Negative Cap',
                    'kind-of-time'                        => 'Kind of Time Off',
                ],
            ],
        ],
    ],
];
