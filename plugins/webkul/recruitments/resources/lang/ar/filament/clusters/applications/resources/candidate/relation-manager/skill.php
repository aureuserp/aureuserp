<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'skill-type'  => 'Skill Type',
                'skill'       => 'Skill',
                'skill-level' => 'Skill Level',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'skill-type'    => 'Skill Type',
            'skill'         => 'Skill',
            'skill-level'   => 'Skill Level',
            'level-percent' => 'Level Percent',
            'created-by'    => 'Created By',
            'user'          => 'User',
            'created-at'    => 'Created At',
        ],

        'groups' => [
            'skill-type' => 'Skill Type',
        ],

        'header-actions' => [
            'add-skill' => 'Add Skill',
        ],

        'filters' => [
            'activity-type'   => 'Activity Type',
            'activity-status' => 'Activity Status',
            'has-delay'       => 'Has Delay',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Skill updated',
                    'body'  => 'The skill has been updated successfully.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Skill created',
                    'body'  => 'The skill has been created successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Skill deleted',
                    'body'  => 'The skill has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Skills deleted',
                    'body'  => 'The skills has been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'skill-type'    => 'Skill Type',
            'skill'         => 'Skill',
            'skill-level'   => 'Skill Level',
            'level-percent' => 'Level Percent',
        ],
    ],
];
