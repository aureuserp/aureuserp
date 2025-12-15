<?php

return [
    'navigation' => [
        'title' => 'Dropships',
        'group' => 'Transfers',
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Dropship deleted',
                        'body'  => 'The dropship ras been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Dropship could not be deleted',
                        'body'  => 'The dropship cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Dropships deleted',
                        'body'  => 'The dropships has been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Dropships could not be deleted',
                        'body'  => 'The dropships cannot be deleted because they are currently in use.',
                    ],
                ],
            ],
        ],
    ],
];
