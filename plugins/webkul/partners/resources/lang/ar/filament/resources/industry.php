<?php

return [
    'form' => [
        'name'      => 'Name',
        'full-name' => 'Full Name',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Name',
            'full-name'  => 'Full Name',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Industry updated',
                    'body'  => 'The industry has been updated successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Industry restored',
                    'body'  => 'The industry has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Industry deleted',
                    'body'  => 'The industry has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Industry force deleted',
                    'body'  => 'The industry has been force deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Industries restored',
                    'body'  => 'The industries has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Industries deleted',
                    'body'  => 'The industries has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Industries force deleted',
                    'body'  => 'The industries has been force deleted successfully.',
                ],
            ],
        ],
    ],
];
