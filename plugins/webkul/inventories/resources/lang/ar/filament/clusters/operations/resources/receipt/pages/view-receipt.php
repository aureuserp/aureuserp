<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Print',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Receipt Deleted',
                    'body'  => 'The receipt has been deleted successfully.',
                ],

                'error' => [
                    'title' => 'Receipt could not be deleted',
                    'body'  => 'The Receipt cannot be deleted because it is currently in use.',
                ],
            ],
        ],
    ],
];
