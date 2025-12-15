<?php

return [
    'form' => [
        'name'          => 'Name',
        'level'         => 'Level',
        'default-level' => 'Default Level',
    ],

    'table' => [
        'columns' => [
            'name'          => 'Name',
            'level'         => 'Level',
            'default-level' => 'Default Level',
            'created-at'    => 'Created At',
            'updated-at'    => 'Updated At',
        ],

        'groups' => [
            'created-at' => 'Created At',
        ],

        'filters' => [
            'deleted-records' => 'Deleted Records',
        ],

        'actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Skill Level created',
                    'body'  => 'The skill level has been created successfully.',
                ],
            ],

            'edit' => [
                'notification' => [
                    'title' => 'Skill Level updated',
                    'body'  => 'The skill level has been updated successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Skill Level restored',
                    'body'  => 'The skill level has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Skill Level deleted',
                    'body'  => 'The skill level has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Skill Levels deleted',
                    'body'  => 'The skills has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Skill Levels force deleted',
                    'body'  => 'The skills has been force deleted successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Skill Levels force restored',
                    'body'  => 'The skills has been force restored successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'          => 'Name',
            'level'         => 'Level',
            'default-level' => 'Default Level',
        ],
    ],
];
