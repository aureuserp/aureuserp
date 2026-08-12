<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'title'        => 'Title',
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
                    'title' => 'Resume updated',
                    'body'  => 'The resume has been updated successfully.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Resume created',
                    'body'  => 'The resume has been created successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Resume deleted',
                    'body'  => 'The resume has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Resumes deleted',
                    'body'  => 'The resumes have been deleted successfully.',
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
