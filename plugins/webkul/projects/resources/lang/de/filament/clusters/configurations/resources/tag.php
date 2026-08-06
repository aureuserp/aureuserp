<?php

return [
    'navigation' => [
        'title' => 'Tags',
    ],

    'form' => [
        'name'  => 'Name',
        'color' => 'Color',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Name',
            'color'      => 'Color',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tag updated',
                    'body'  => 'The tag has been updated successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Tag restored',
                    'body'  => 'The tag has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tag deleted',
                    'body'  => 'The tag has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tag force deleted',
                    'body'  => 'The tag has been force deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tags restored',
                    'body'  => 'The tags has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tags deleted',
                    'body'  => 'The tags has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tags force deleted',
                    'body'  => 'The tags has been force deleted successfully.',
                ],
            ],
        ],
    ],
];
