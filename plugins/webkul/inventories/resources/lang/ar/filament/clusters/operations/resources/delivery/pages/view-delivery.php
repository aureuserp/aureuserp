<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Print',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Delivery Deleted',
                    'body'  => 'The delivery has been deleted successfully.',
                ],

                'error' => [
                    'title' => 'Delivery could not be deleted',
                    'body'  => 'The delivery cannot be deleted because it is currently in use.',
                ],
            ],
        ],
    ],
];
