<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Print',

            'actions' => [
                'without-content' => [
                    'label' => 'Print Barcode',
                ],

                'with-content' => [
                    'label' => 'Print Barcode With Content',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Package Deleted',
                    'body'  => 'The package has been deleted successfully.',
                ],

                'error' => [
                    'title' => 'Package could not be deleted',
                    'body'  => 'The package cannot be deleted because it is currently in use.',
                ],
            ],
        ],
    ],
];
