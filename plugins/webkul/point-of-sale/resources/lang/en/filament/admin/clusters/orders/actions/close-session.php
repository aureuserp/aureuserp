<?php

return [
    'label' => 'Close Session',

    'form' => [
        'fields' => [
            'cash-balance-end-real'             => 'Counted Cash',
            'cash-balance-end-real-helper-text' => 'Expected balance: :expected',
            'closing-notes'                     => 'Notes',
            'balancing-account'                 => 'Balancing Account',
            'balancing-account-helper-text'     => 'Only used when the closing entry does not balance.',
        ],
    ],

    'notification' => [
        'unbalanced' => [
            'title' => 'Closing entry out of balance',
        ],

        'success' => [
            'title' => 'Session closed',
            'body'  => 'The session has been closed.',
        ],
    ],
];
