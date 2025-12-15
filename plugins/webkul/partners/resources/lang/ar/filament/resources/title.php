<?php

return [
    'form' => [
        'name'       => 'Name',
        'short-name' => 'Short Name',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Name',
            'short-name' => 'Short Name',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'filters' => [
            'creator' => 'Creator',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Title updated',
                    'body'  => 'The Title has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Title deleted',
                    'body'  => 'The title has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Titles deleted',
                    'body'  => 'The titles has been deleted successfully.',
                ],
            ],
        ],
    ],
];
