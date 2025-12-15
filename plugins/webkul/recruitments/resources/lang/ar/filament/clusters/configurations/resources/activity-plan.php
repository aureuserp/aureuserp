<?php

return [
    'navigation' => [
        'title' => 'Activity Plans',
        'group' => 'Activities',
    ],

    'global-search' => [
        'name'         => 'Department',
        'department'   => 'Department',
        'manager'      => 'Manager',
        'company'      => 'Company',
        'plugin'       => 'Plugin',
        'creator-name' => 'Created By',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Name',
            'status'     => 'Status',
            'department' => 'Department',
            'company'    => 'Company',
            'manager'    => 'Manager',
            'created-by' => 'Created By',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'filters' => [
            'name'           => 'Name',
            'plugin'         => 'Plugin',
            'activity-types' => 'Activity Types',
            'company'        => 'Company',
            'department'     => 'Department',
            'is-active'      => 'Status',
            'updated-at'     => 'Updated At',
            'created-at'     => 'Created At',
        ],

        'groups' => [
            'status'     => 'Status',
            'name'       => 'Name',
            'created-by' => 'Created By',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Activity Plan restored',
                    'body'  => 'The activity plan has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Activity Plan deleted',
                    'body'  => 'The activity plan has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Activity Plan force deleted',
                    'body'  => 'The activity plan has been force deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Activity Plans restored',
                    'body'  => 'The activity plans has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Activity Plans deleted',
                    'body'  => 'The activity plans has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Activity Plans force deleted',
                    'body'  => 'The activity plans has been force deleted successfully.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Activity Plan created',
                    'body'  => 'The activity plan has been created successfully.',
                ],
            ],
        ],
    ],
];
