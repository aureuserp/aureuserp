<?php

return [
    'navigation' => [
        'title' => 'Operations',
        'group' => 'Configuration',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'name'              => 'Operation',
                    'name-placeholder'  => 'eg. Cutting',
                    'bill-of-material'  => 'Bill of Material',
                    'work-center'       => 'Work Center',
                    'apply-on-variants' => 'Apply on Variants',
                    'company'           => 'Company',
                    'blocked-by'        => 'Blocked By',
                ],
            ],
            'settings' => [
                'title'  => 'Settings',
                'fields' => [
                    'time-mode'                  => 'Duration Computation',
                    'time-mode-batch'            => 'Based On',
                    'time-mode-batch-prefix'     => 'last',
                    'time-mode-batch-suffix'     => 'work orders',
                    'manual-cycle-time'          => 'Default Duration',
                    'manual-cycle-time-suffix'   => 'minutes',
                ],
            ],
            'worksheet' => [
                'title'  => 'Work Sheet',
                'fields' => [
                    'worksheet'                => 'Worksheet',
                    'pdf'                      => 'PDF',
                    'google-slide'             => 'Google Slide',
                    'google-slide-placeholder' => 'Google Slide Link',
                    'description'              => 'Description',
                    'description-placeholder'  => 'Description of the operation...',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'              => 'Operation',
            'bill-of-material'  => 'Bill of Material',
            'work-center'       => 'Work Center',
            'time-mode'         => 'Duration Computation',
            'manual-cycle-time' => 'Default Duration',
            'worksheet-type'    => 'Worksheet',
            'deleted-at'        => 'Deleted At',
            'created-at'        => 'Created At',
            'updated-at'        => 'Updated At',
        ],
        'filters' => [
            'work-center'    => 'Work Center',
            'time-mode'      => 'Duration Computation',
            'worksheet-type' => 'Worksheet',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Operation restored',
                    'body'  => 'The operation has been restored successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Operation archived',
                    'body'  => 'The operation has been archived successfully.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Operation deleted',
                        'body'  => 'The operation has been permanently deleted.',
                    ],
                    'error' => [
                        'title' => 'Operation could not be deleted',
                        'body'  => 'The operation cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Operations restored',
                    'body'  => 'The selected operations have been restored successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Operations archived',
                    'body'  => 'The selected operations have been archived successfully.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Operations deleted',
                        'body'  => 'The selected operations have been permanently deleted.',
                    ],
                    'error' => [
                        'title' => 'Operations could not be deleted',
                        'body'  => 'One or more selected operations are currently in use.',
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
                    'name'              => 'Operation',
                    'bill-of-material'  => 'Bill of Material',
                    'work-center'       => 'Work Center',
                    'apply-on-variants' => 'Apply on Variants',
                    'company'           => 'Company',
                ],
            ],
            'settings' => [
                'title'   => 'Settings',
                'entries' => [
                    'time-mode'                => 'Duration Computation',
                    'time-mode-batch'          => 'Based On',
                    'manual-cycle-time'        => 'Default Duration',
                    'manual-cycle-time-suffix' => 'minutes',
                ],
            ],
            'worksheet' => [
                'title'   => 'Work Sheet',
                'entries' => [
                    'worksheet'    => 'Worksheet',
                    'pdf'          => 'PDF',
                    'google-slide' => 'Google Slide',
                    'description'  => 'Description',
                ],
            ],
            'record-information' => [
                'title'   => 'Record Information',
                'entries' => [
                    'created-by'   => 'Created By',
                    'created-at'   => 'Created At',
                    'last-updated' => 'Last Updated',
                ],
            ],
        ],
    ],
];
