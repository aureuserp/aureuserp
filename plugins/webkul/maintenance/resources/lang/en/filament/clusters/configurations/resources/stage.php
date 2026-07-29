<?php

return [
    'navigation' => [
        'title' => 'Stages',
    ],

    'form' => [
        'fields' => [
            'name' => 'Name',
            'done' => 'Done',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Name',
            'done'       => 'Done',
            'created-at' => 'Created At',
        ],

        'groups' => [
            'done'       => 'Done',
            'created-at' => 'Created At',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Stage updated',
                    'body'  => 'The stage has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Stage deleted',
                    'body'  => 'The stage has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Stages deleted',
                    'body'  => 'The stages have been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General Information',

                'entries' => [
                    'name' => 'Name',
                    'done' => 'Done',
                ],
            ],
        ],
    ],
];
