<?php

return [
    'label' => 'Create Bill',

    'action' => [
        'notification' => [
            'warning' => [
                'title' => 'No invoiceable lines',
                'body'  => 'There is no invoiceable line, please make sure that a quantity has been received.',
            ],

            'missing-journal' => [
                'title' => 'Accounting is not set up',
            ],

            'success' => [
                'title' => 'Bill created',
                'body'  => 'The bill has been created successfully.',
            ],
        ],
    ],
];
