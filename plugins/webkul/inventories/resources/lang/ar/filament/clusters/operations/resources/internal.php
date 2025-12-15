<?php

return [
    'navigation' => [
        'title' => 'Internal Transfers',
        'group' => 'Transfers',
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Internal Transfer deleted',
                        'body'  => 'The internal transfer ras been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Internal Transfer could not be deleted',
                        'body'  => 'The internal transfer cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Internal Transfers deleted',
                        'body'  => 'The internal transfers has been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Internal Transfers could not be deleted',
                        'body'  => 'The internal transfers cannot be deleted because they are currently in use.',
                    ],
                ],
            ],
        ],
    ],
];
