<?php

return [
    'stats' => [
        'open-sessions' => [
            'label'       => 'Open Sessions',
            'description' => 'Sessions still on the counter',
        ],

        'orders' => [
            'label'       => 'Orders',
            'description' => 'Settled orders in the selected period',
        ],

        'revenue' => [
            'label'       => 'Revenue',
            'description' => 'Total of settled orders in the selected period',
        ],

        'failed-operations' => [
            'label'       => 'Failed Operations',
            'description' => 'Orders whose stock move needs attention',
        ],
    ],
];
