<?php

return [
    'navigation' => [
        'group' => 'Maintenance',
        'title' => 'Maintenance Requests',
    ],

    'form' => [
        'sections' => [
            'request' => [
                'title'  => 'Request',
                'fields' => [
                    'name'                      => 'Request',
                    'name-placeholder'          => 'e.g. Screen not working',
                    'equipment'                 => 'Equipment',
                    'category'                  => 'Category',
                    'requested-at'              => 'Request Date',
                    'requested-at-hint-tooltip' => 'The date the maintenance request was reported.',
                    'maintenance-type'          => 'Maintenance Type',
                    'recurrent'                 => 'Recurrent',
                    'repeat-every'              => 'Repeat Every',
                    'maintenance-type-options'  => [
                        'corrective' => 'Corrective',
                        'preventive' => 'Preventive',
                    ],
                ],
                'tabs' => [
                    'notes' => [
                        'title'  => 'Notes',
                        'fields' => [
                            'description'             => 'Internal Notes',
                            'description-placeholder' => 'Internal notes',
                        ],
                    ],
                    'instructions' => [
                        'title'  => 'Instructions',
                        'fields' => [
                            'instruction-type'         => 'Instruction Type',
                            'instruction-type-options' => [
                                'pdf'          => 'PDF',
                                'google-slide' => 'Google Slide',
                                'text'         => 'Text',
                            ],
                            'instruction-pdf'              => 'PDF',
                            'instruction-google-slide'     => 'Google Slide',
                            'instruction-text'             => 'Description',
                            'instruction-text-placeholder' => 'Description',
                        ],
                    ],
                ],
            ],

            'settings' => [
                'title'  => 'Settings',
                'fields' => [
                    'team'                      => 'Team',
                    'responsible'               => 'Responsible',
                    'scheduled-at'              => 'Scheduled Date',
                    'scheduled-at-hint-tooltip' => 'The date and time this maintenance work is planned to start.',
                    'duration'                  => 'Duration',
                    'duration-hint-tooltip'     => 'Expected maintenance duration.',
                    'duration-suffix'           => 'hours',
                    'priority'                  => 'Priority',
                    'company'                   => 'Company',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Subjects',
            'creator'    => 'Created by User',
            'technician' => 'Technician',
            'category'   => 'Category',
            'stage'      => 'Stage',
            'company'    => 'Company',
        ],

        'groups' => [
            'stage'       => 'Stage',
            'assigned-to' => 'Assigned to',
            'category'    => 'Category',
            'created-by'  => 'Created By',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Maintenance request restored',
                    'body'  => 'The maintenance request has been restored successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Maintenance request archived',
                    'body'  => 'The maintenance request has been archived successfully.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Maintenance request deleted',
                        'body'  => 'The maintenance request has been permanently deleted.',
                    ],
                    'error' => [
                        'title' => 'Maintenance request could not be deleted',
                        'body'  => 'This maintenance request is referenced by another record.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Maintenance requests restored',
                    'body'  => 'The selected maintenance requests have been restored successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Maintenance requests archived',
                    'body'  => 'The selected maintenance requests have been archived successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'request' => [
                'title'   => 'Request',
                'entries' => [
                    'name'                     => 'Request',
                    'equipment'                => 'Equipment',
                    'category'                 => 'Category',
                    'requested-at'             => 'Request Date',
                    'maintenance-type'         => 'Maintenance Type',
                    'instruction-type'         => 'Instruction Type',
                    'instruction-pdf'          => 'PDF',
                    'instruction-google-slide' => 'Google Slide',
                    'description'              => 'Internal Notes',
                    'instruction-text'         => 'Description',
                ],
            ],

            'settings' => [
                'title'   => 'Settings',
                'entries' => [
                    'team'            => 'Team',
                    'responsible'     => 'Responsible',
                    'scheduled-at'    => 'Scheduled Date',
                    'duration'        => 'Duration',
                    'duration-suffix' => 'hours',
                    'priority'        => 'Priority',
                    'company'         => 'Company',
                ],
            ],
        ],
    ],
];
