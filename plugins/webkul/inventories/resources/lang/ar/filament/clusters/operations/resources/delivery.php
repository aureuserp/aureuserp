<?php

return [
    'navigation' => [
        'title' => 'Deliveries',
        'group' => 'Transfers',
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Delivery deleted',
                        'body'  => 'The delivery ras been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Delivery could not be deleted',
                        'body'  => 'The delivery cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Deliveries deleted',
                        'body'  => 'The deliveries has been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Deliveries could not be deleted',
                        'body'  => 'The deliveries cannot be deleted because they are currently in use.',
                    ],
                ],
            ],
        ],
    ],
];
