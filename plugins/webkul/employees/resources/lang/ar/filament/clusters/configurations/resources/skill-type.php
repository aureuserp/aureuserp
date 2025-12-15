<?php

return [
    'title' => 'Skill Types',

    'navigation' => [
        'title' => 'Skill Types',
        'group' => 'Employee',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'       => 'Name',
                'color'      => 'Color',
                'status'     => 'Status',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Skill Type',
            'status'     => 'Status',
            'color'      => 'Color',
            'skills'     => 'Skills',
            'levels'     => 'Levels',
            'created-by' => 'Created By',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'filters' => [
            'skill-levels' => 'Skill Levels',
            'skills'       => 'Skills',
            'created-by'   => 'Created By',
            'status'       => 'Status',
            'updated-at'   => 'Updated At',
            'created-at'   => 'Created At',
        ],

        'groups' => [
            'name'       => 'Skill Type',
            'color'      => 'Color',
            'status'     => 'Status',
            'created-by' => 'Created By',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Skill Type restored',
                    'body'  => 'The Skill Type has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Skill Type deleted',
                    'body'  => 'The Skill Type has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Skill Types restored',
                    'body'  => 'The Skill Types has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Skill Types deleted',
                    'body'  => 'The Skill Types has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Skill Types force deleted',
                    'body'  => 'The Skill Types has been force deleted successfully.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Skill Types',
                    'body'  => 'The Skill Types has been created successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'   => 'Skill Type',
                'color'  => 'Color',
                'status' => 'Status',
            ],
        ],
    ],
];
