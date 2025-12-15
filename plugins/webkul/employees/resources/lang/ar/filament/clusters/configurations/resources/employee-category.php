<?php

return [
    'title' => 'Tags',

    'navigation' => [
        'title' => 'Tags',
        'group' => 'Employee',
    ],

    'groups' => [
        'status'     => 'Status',
        'created-by' => 'Created By',
        'created-at' => 'Created At',
        'updated-at' => 'Updated At',
    ],

    'form' => [
        'fields' => [
            'name'  => 'Name',
            'color' => 'Color',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Name',
            'color'      => 'Color',
            'created-by' => 'Created By',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'filters' => [
            'name'       => 'Name',
            'created-by' => 'Created By',
            'updated-by' => 'Updated By',
            'updated-at' => 'Updated At',
            'created-at' => 'Created At',
        ],

        'groups' => [
            'name'         => 'Name',
            'job-position' => 'Job Position',
            'color'        => 'Color',
            'created-by'   => 'Created By',
            'created-at'   => 'Created At',
            'updated-at'   => 'Updated At',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tag updated',
                    'body'  => 'The tag has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tag deleted',
                    'body'  => 'The tag has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tags deleted',
                    'body'  => 'The tags has been deleted successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Tag created',
                    'body'  => 'The tag has been created successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'  => 'Name',
        'color' => 'Color',
    ],
];
