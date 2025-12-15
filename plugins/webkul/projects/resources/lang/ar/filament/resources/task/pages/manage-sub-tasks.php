<?php

return [
    'title' => 'Sub Tasks',

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Add Sub Task',

                'notification' => [
                    'title' => 'Task created',
                    'body'  => 'The task has been created successfully.',
                ],
            ],
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Task restored',
                    'body'  => 'The task has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Task deleted',
                    'body'  => 'The task has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Task force deleted',
                    'body'  => 'The task has been force deleted successfully.',
                ],
            ],
        ],
    ],
];
