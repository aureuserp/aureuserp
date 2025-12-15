<?php

return [
    'title' => 'Departments',

    'navigation' => [
        'title' => 'Departments',
        'group' => 'Employees',
    ],

    'form' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'General Information',

                'fields' => [
                    'name'                => 'Activity Type',
                    'name-tooltip'        => 'Enter the official activity type name',
                    'action'              => 'Action',
                    'default-user'        => 'Default User',
                    'summary'             => 'Summary',
                    'note'                => 'Note',
                ],
            ],

            'delay-information' => [
                'title' => 'Delay Information',

                'fields' => [
                    'delay-count'            => 'Delay Count',
                    'delay-unit'             => 'Delay Unit',
                    'delay-form'             => 'Delay Form',
                    'delay-form-helper-text' => 'Source of delay calculation',
                ],
            ],

            'advanced-information' => [
                'title' => 'Advanced Information',

                'fields' => [
                    'icon'                => 'Icon',
                    'decoration-type'     => 'Decoration Type',
                    'chaining-type'       => 'Chaining Type',
                    'suggest'             => 'Suggest',
                    'trigger'             => 'Trigger',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Status & Configuration',

                'fields' => [
                    'status'               => 'Status',
                    'keep-done-activities' => 'Keep Done Activities',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Activity Type',
            'summary'    => 'Summary',
            'planned-in' => 'Planned In',
            'type'       => 'Type',
            'action'     => 'Action',
            'status'     => 'Status',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'groups' => [
            'name'             => 'Name',
            'action-category'  => 'Action Category',
            'status'           => 'Status',
            'delay-count'      => 'Delay Count',
            'delay-unit'       => 'Delay Unit',
            'delay-source'     => 'Delay Source',
            'associated-model' => 'Associated Model',
            'chaining-type'    => 'Chaining Type',
            'decoration-type'  => 'Decoration Type',
            'default-user'     => 'Default User',
            'creation-date'    => 'Creation Date',
            'last-update'      => 'Last Update',
        ],

        'filters' => [
            'action'    => 'Action',
            'status'    => 'Status',
            'has-delay' => 'Has Delay',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Activity type restored',
                    'body'  => 'The activity type has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Activity type deleted',
                    'body'  => 'The activity type has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Activity type force deleted',
                        'body'  => 'The Activity type has been force deleted successfully.',
                    ],
                    'error' => [
                        'title' => 'Activity type  could not be deleted',
                        'body'  => 'The Activity type  cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Activity types restored',
                    'body'  => 'The activity types has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Activity types deleted',
                    'body'  => 'The activity types has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Activity types force deleted',
                    'body'  => 'The activity types has been force deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'General Information',

                'entries' => [
                    'name'                => 'Activity Type',
                    'name-tooltip'        => 'Enter the official activity type name',
                    'action'              => 'Action',
                    'default-user'        => 'Default User',
                    'plugin'              => 'Plugin',
                    'summary'             => 'Summary',
                    'note'                => 'Note',
                ],
            ],

            'delay-information' => [
                'title' => 'Delay Information',

                'entries' => [
                    'delay-count'            => 'Delay Count',
                    'delay-unit'             => 'Delay Unit',
                    'delay-form'             => 'Delay Form',
                    'delay-form-helper-text' => 'Source of delay calculation',
                ],
            ],

            'advanced-information' => [
                'title' => 'Advanced Information',

                'entries' => [
                    'icon'                => 'Icon',
                    'decoration-type'     => 'Decoration Type',
                    'chaining-type'       => 'Chaining Type',
                    'suggest'             => 'Suggest',
                    'trigger'             => 'Trigger',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Status & Configuration',

                'entries' => [
                    'status'               => 'Status',
                    'keep-done-activities' => 'Keep Done Activities',
                ],
            ],
        ],
    ],
];
