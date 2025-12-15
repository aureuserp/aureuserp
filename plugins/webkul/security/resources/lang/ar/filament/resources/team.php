<?php

return [
    'title' => 'Teams',

    'navigation' => [
        'title' => 'Teams',
        'group' => 'Settings',
    ],

    'form' => [
        'fields' => [
            'name' => 'Name',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Name',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Team updated',
                    'body'  => 'The team has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Team deleted',
                    'body'  => 'The team has been deleted successfully.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Teams created',
                    'body'  => 'The teams has been created successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'          => 'Name',
                'job-title'     => 'Job Title',
                'work-email'    => 'Work Email',
                'work-mobile'   => 'Work Mobile',
                'work-phone'    => 'Work Phone',
                'manager'       => 'Manager',
                'department'    => 'Department',
                'job-position'  => 'Job Position',
                'team-tags'     => 'Team Tags',
                'coach'         => 'Coach',
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Name',
        ],
    ],
];
