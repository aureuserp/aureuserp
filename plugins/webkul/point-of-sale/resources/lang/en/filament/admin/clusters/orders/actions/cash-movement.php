<?php

return [
    'label' => 'Cash In/Out',

    'form' => [
        'fields' => [
            'type'   => 'Type',
            'amount' => 'Amount',
            'reason' => 'Reason',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Cash movement recorded',
            'body'  => 'The cash drawer balance has been updated.',
        ],
    ],
];
