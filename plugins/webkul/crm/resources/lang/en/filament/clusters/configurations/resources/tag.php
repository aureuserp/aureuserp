<?php

return [
    'model-label' => 'Tag',

    'navigation' => [
        'title' => 'Tags',
        'group' => 'Pipeline',
    ],

    'form' => [
        'fields' => [
            'name'  => 'Tag Name',
            'color' => 'Color',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Tag Name',
            'color'      => 'Color',
            'created-by' => 'Created By',
            'created-at' => 'Created At',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tag updated',
                    'body'  => 'The tag has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tag deleted',
                    'body'  => 'The tag has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tags deleted',
                    'body'  => 'The tags have been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'  => 'Tag Name',
            'color' => 'Color',
        ],
    ],
];
