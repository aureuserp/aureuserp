<?php

return [
    'notification' => [
        'title' => 'Internal Transfer updated',
        'body'  => 'The internal transfer has been updated successfully.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Print',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Internal Transfer deleted',
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
