<?php

return [
    'title' => 'Forecast',

    'navigation' => [
        'title' => 'Forecast',
    ],

    'kanban' => [
        'filters' => [
            'salesperson' => 'Salesperson',
            'team'        => 'Sales Team',
            'period'      => 'Expected Closing',
            'upcoming'    => 'Upcoming closings',
            'all-dates'   => 'Any date',
            'all'         => 'All',
        ],

        'empty-state' => [
            'heading'     => 'No opportunities to forecast',
            'description' => 'Set an expected closing date on your opportunities to see them here.',
        ],

        'notifications' => [
            'moved'        => 'Expected closing date updated',
            'unauthorized' => 'You are not allowed to change this opportunity.',
        ],
    ],
];
