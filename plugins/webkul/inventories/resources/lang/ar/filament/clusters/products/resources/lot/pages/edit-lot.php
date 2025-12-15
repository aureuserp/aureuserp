<?php

return [
    'notification' => [
        'title' => 'Lot updated',
        'body'  => 'The lot has been updated successfully.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Print',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Lot deleted',
                    'body'  => 'The lot has been deleted successfully.',
                ],

                'error' => [
                    'title' => 'Lot could not be deleted',
                    'body'  => 'The lot cannot be deleted because it is currently in use.',
                ],
            ],
        ],
    ],
];
