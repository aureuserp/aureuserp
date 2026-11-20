<?php

return [
    'title' => 'Opportunity',

    'chatter' => [
        'won'      => ':model marked as won',
        'lost'     => ':model marked as lost',
        'restored' => ':model moved back to the pipeline',
    ],

    'log-attributes' => [
        'name'             => 'Opportunity',
        'stage'            => 'Stage',
        'customer'         => 'Customer',
        'salesperson'      => 'Salesperson',
        'team'             => 'Sales Team',
        'expected-revenue' => 'Expected Revenue',
        'probability'      => 'Probability',
        'lost-reason'      => 'Lost Reason',
    ],
];
