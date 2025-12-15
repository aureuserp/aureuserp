<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'title'        => 'Title',
                'type'         => 'Type',
                'name'         => 'Name',
                'type'         => 'Type',
                'create-type'  => 'Create Type',
                'duration'     => 'Duration',
                'start-date'   => 'Start Date',
                'end-date'     => 'End Date',
                'display-type' => 'Display Type',
                'description'  => 'Description',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'Title',
            'start-date'   => 'Start Date',
            'end-date'     => 'End Date',
            'display-type' => 'Display Type',
            'description'  => 'Description',
            'created-by'   => 'Created By',
            'created-at'   => 'Created At',
            'updated-at'   => 'Updated At',
        ],

        'groups' => [
            'group-by-type'         => 'Group By Type',
            'group-by-display-type' => 'Group By Display Type',
        ],

        'header-actions' => [
            'add-resume' => 'Add Resume',
        ],

        'filters' => [
            'type'            => 'Type',
            'start-date-from' => 'Start Date From',
            'start-date-to'   => 'Start Date To',
            'created-from'    => 'Created From',
            'created-to'      => 'Created To',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Skill Level updated',
                    'body'  => 'The skill level has been updated successfully.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Skill Level created',
                    'body'  => 'The skill level has been created successfully.',
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
                    'title' => 'Skills deleted',
                    'body'  => 'The skills has been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'title'        => 'Title',
            'display-type' => 'Display Type',
            'type'         => 'Type',
            'description'  => 'Description',
            'duration'     => 'Duration',
            'start-date'   => 'Start Date',
            'end-date'     => 'End Date',
        ],
    ],
];
