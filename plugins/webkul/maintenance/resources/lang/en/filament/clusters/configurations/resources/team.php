<?php

return [
    'navigation' => [
        'title' => 'Teams',
    ],

    'form' => [
        'name'    => 'Name',
        'company' => 'Company',
        'users'   => 'Team Members',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Name',
            'company'    => 'Company',
            'users'      => 'Team Members',
            'created-at' => 'Created At',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Team updated',
                    'body'  => 'The team has been updated successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Team restored',
                    'body'  => 'The team has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Team deleted',
                    'body'  => 'The team has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Team force deleted',
                        'body'  => 'The team has been force deleted successfully.',
                    ],
                    'error' => [
                        'title' => 'Team could not be force deleted',
                        'body'  => 'The team is being used and cannot be force deleted.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Teams restored',
                    'body'  => 'The teams have been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Teams deleted',
                    'body'  => 'The teams have been deleted successfully.',
                ],
            ],
        ],
    ],
];
