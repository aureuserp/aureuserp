<?php

return [
    'notification' => [
        'title' => 'Location updated',
        'body'  => 'The location has been updated successfully.',

        'error' => [
            'title' => 'Location could not be updated',
        ],
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Print',
        ],

        'delete' => [
            'notification' => [
                'title' => 'Location deleted',
                'body'  => 'The location has been deleted successfully.',
            ],
        ],
    ],
];
