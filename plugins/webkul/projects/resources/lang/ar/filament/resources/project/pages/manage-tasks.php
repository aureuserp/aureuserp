<?php

return [
    'title' => 'Tasks',

    'header-actions' => [
        'create' => [
            'label' => 'New Task',
        ],
    ],

    'table' => [
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

    'tabs' => [
        'open-tasks'       => 'Open Tasks',
        'my-tasks'         => 'My Tasks',
        'unassigned-tasks' => 'Unassigned Tasks',
        'closed-tasks'     => 'Closed Tasks',
        'starred-tasks'    => 'Starred Tasks',
        'archived-tasks'   => 'Archived Tasks',
    ],
];
