<?php

return [
    'title'        => 'Session',
    'plural-title' => 'Sessions',

    'log-attributes' => [
        'name'                  => 'Session',
        'state'                 => 'Status',
        'cash-balance-start'    => 'Opening Balance',
        'cash-balance-end-real' => 'Counted Balance',
        'cash-difference'       => 'Difference',
    ],

    'chatter' => [
        'opening' => [
            'difference' => 'Opening cash difference: :amount',
            'expected'   => 'Opening cash expected: :amount',
            'counted'    => 'Opening cash counted: :amount',
            'message'    => 'Opening control message: :message',
        ],

        'closing' => [
            'difference' => 'Closing difference: :amount',
            'expected'   => 'Closing expected: :amount',
            'counted'    => 'Closing counted: :amount',
            'message'    => 'Closing control message: :message',
        ],

        'cash-movement' => [
            'in'     => 'Cash in: :amount',
            'out'    => 'Cash out: :amount',
            'reason' => 'Reason: :reason',
        ],
    ],
];
