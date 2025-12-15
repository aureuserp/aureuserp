<?php

return [
    'title' => 'Refuse Reason',

    'navigation' => [
        'title' => 'Refuse Reasons',
        'group' => 'Applications',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Name',
            'template'         => [
                'title'                    => 'Template',
                'applicant-refuse'         => 'Applicant Refuse',
                'applicant-not-interested' => 'Applicant Not Interested',
            ],
            'name-placeholder' => 'Enter the name of the refuse reason',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Name',
            'template'   => 'Template',
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
                    'title' => 'Refuse reason updated',
                    'body'  => 'The refuse reason has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Refuse reason deleted',
                    'body'  => 'The refuse reason has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Refuse reasons deleted',
                    'body'  => 'The refuse reasons has been deleted successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Refuse reason created',
                    'body'  => 'The refuse reason has been created successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'       => 'Name',
        'template'   => 'Template',
    ],
];
