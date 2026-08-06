<?php

return [
    'navigation' => [
        'title' => 'Activity Plans',
    ],

    'form' => [
        'name'   => 'Name',
        'status' => 'Status',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Name',
            'status'     => 'Status',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'groups' => [
            'name'       => 'Name',
            'status'     => 'Status',
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

    'infolist' => [
        'name'   => 'Name',
        'status' => 'Status',
    ],
];
