<?php

return [
    'notification' => [
        'title' => 'Dropship updated',
        'body'  => 'The dropship has been updated successfully.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Print',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Dropship deleted',
                    'body'  => 'The dropship has been deleted successfully.',
                ],

                'error' => [
                    'title' => 'Dropship could not be deleted',
                    'body'  => 'The dropship cannot be deleted because it is currently in use.',
                ],
            ],
        ],
    ],
];
