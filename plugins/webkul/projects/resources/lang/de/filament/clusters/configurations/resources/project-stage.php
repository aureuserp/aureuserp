<?php

return [
    'navigation' => [
        'title' => 'Project Stages',
    ],

    'form' => [
        'name' => 'Name',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Name',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'groups' => [
            'name'         => 'Name',
            'is-completed' => 'Is Completed',
            'project'      => 'Project',
            'created-at'   => 'Created At',
        ],

        'filters' => [
            'is-completed' => 'Is Completed',
            'project'      => 'Project',
            'creator'      => 'Creator',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Project stage updated',
                    'body'  => 'The project stage has been updated successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Project stage restored',
                    'body'  => 'The project stage has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Project stage deleted',
                    'body'  => 'The project stage has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Project stage force deleted',
                        'body'  => 'The project stage has been force deleted successfully.',
                    ],
                    'error' => [
                        'title' => 'Project Stage  could not be deleted',
                        'body'  => 'The Project Stage  cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Project stages restored',
                    'body'  => 'The project stages has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Project stages deleted',
                    'body'  => 'The project stages has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Project stages force deleted',
                    'body'  => 'The project stages has been force deleted successfully.',
                ],
            ],
        ],
    ],
];
