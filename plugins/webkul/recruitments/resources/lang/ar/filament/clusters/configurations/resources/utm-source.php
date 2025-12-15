<?php

return [
    'title' => 'Sources',

    'navigation' => [
        'title' => 'Sources',
        'group' => 'UTMs',
    ],

    'groups' => [
        'status'     => 'Status',
        'created-by' => 'Created By',
        'created-at' => 'Created At',
        'updated-at' => 'Updated At',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Name',
            'name-placeholder' => 'Enter the name of the source',
            'status'           => 'Status',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Name',
            'status'     => 'Status',
            'created-by' => 'Created By',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'filters' => [
            'name'       => 'Name',
            'created-by' => 'Created By',
            'updated-at' => 'Updated At',
            'created-at' => 'Created At',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Source updated',
                    'body'  => 'The source has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Source deleted',
                    'body'  => 'The source has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Sources deleted',
                    'body'  => 'The Sources has been deleted successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Source created',
                    'body'  => 'The source has been created successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Name',
    ],
];
