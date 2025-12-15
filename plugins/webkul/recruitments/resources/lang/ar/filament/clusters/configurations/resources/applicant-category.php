<?php

return [
    'title' => 'Tags',

    'navigation' => [
        'title' => 'Tags',
        'group' => 'Applications',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Name',
            'name-placeholder' => 'Enter the name of the tags',
            'color'            => 'Color',
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
            'employee'   => 'Employee',
            'created-by' => 'Created By',
            'updated-at' => 'Updated At',
            'created-at' => 'Created At',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tags updated',
                    'body'  => 'The tags has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tags deleted',
                    'body'  => 'The tags has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Applicant Categories deleted',
                    'body'  => 'The applicant Categories has been deleted successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Tags created',
                    'body'  => 'The tags has been created successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'  => 'Name',
        'color' => 'Color',
    ],
];
