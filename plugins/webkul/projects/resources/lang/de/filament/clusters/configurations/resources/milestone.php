<?php

return [
    'navigation' => [
        'title' => 'Milestones',
    ],

    'form' => [
        'name'         => 'Name',
        'deadline'     => 'Deadline',
        'is-completed' => 'Is Completed',
        'project'      => 'Project',
    ],

    'table' => [
        'columns' => [
            'name'         => 'Name',
            'deadline'     => 'Deadline',
            'is-completed' => 'Is Completed',
            'completed-at' => 'Completed At',
            'project'      => 'Project',
            'creator'      => 'Creator',
            'created-at'   => 'Created At',
            'updated-at'   => 'Updated At',
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
                    'title' => 'Milestone update',
                    'body'  => 'The milestone has been update successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Milestone deleted',
                    'body'  => 'The milestone has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Milestones deleted',
                    'body'  => 'The milestones has been deleted successfully.',
                ],
            ],
        ],
    ],
];
