<?php

return [
    'notification' => [
        'title' => 'Bill updated',
        'body'  => 'The bill has been updated successfully.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Bill deleted',
                'body'  => 'Bill has been deleted successfully.',
            ],
        ],

        'preview' => [
            'modal-heading' => 'Preview Bill',
        ],

        'reverse' => [
            'label'         => 'Refund',
            'modal-heading' => 'Create Refund',
        ],
    ],
];
