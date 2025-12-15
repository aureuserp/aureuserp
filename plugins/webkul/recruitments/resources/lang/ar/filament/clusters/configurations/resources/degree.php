<?php

return [
    'title' => 'Degrees',

    'navigation' => [
        'title' => 'Degrees',
        'group' => 'Applications',
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
            'name-placeholder' => 'Enter the name of the degree',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Name',
            'created-by' => 'Created By',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'filters' => [
            'name'       => 'Name',
            'employee'   => 'Employee',
            'created-by' => 'Created By',
            'updated-at' => 'Updated At',
            'created-at' => 'Created At',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Degree updated',
                    'body'  => 'The degree has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Degree deleted',
                    'body'  => 'The degree has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Degrees deleted',
                    'body'  => 'The degrees has been deleted successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Degree created',
                    'body'  => 'The degree has been created successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Name',
    ],
];
