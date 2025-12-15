<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Print',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Lot Deleted',
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
