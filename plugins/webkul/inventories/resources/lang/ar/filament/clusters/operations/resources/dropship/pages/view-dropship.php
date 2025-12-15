<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Print',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Dropship Deleted',
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
