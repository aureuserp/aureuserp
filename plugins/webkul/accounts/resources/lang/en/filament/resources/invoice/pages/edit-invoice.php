<?php

return [
    'notification' => [
        'title' => 'Invoice updated',
        'body'  => 'The invoice has been updated successfully.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Invoice deleted',
                'body'  => 'The invoice has been deleted successfully.',
            ],
        ],

        'reverse' => [
            'label'         => 'Credit Note',
            'modal-heading' => 'Create Credit Note',
        ],
    ],
];
