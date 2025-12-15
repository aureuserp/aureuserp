<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Print',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Internal Transfer Deleted',
                    'body'  => 'The internal transfer has been deleted successfully.',
                ],

                'error' => [
                    'title' => 'Internal Transfer could not be deleted',
                    'body'  => 'The internal transfer cannot be deleted because it is currently in use.',
                ],
            ],
        ],
    ],
];
