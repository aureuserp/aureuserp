<?php

return [
    'none' => 'None',

    'views' => [
        'kanban'  => 'Kanban',
        'graph'   => 'Graph',
        'summary' => 'Summary',
        'list'    => 'List',
    ],

    'chart' => [
        'heading' => ':measure by :group',
    ],

    'filters' => [
        'measure'        => 'Measure',
        'split-by'       => 'Split by',
        'no-split'       => 'No split',
        'chart-type'     => 'Chart',
        'salespeople'    => 'Salespeople',
        'teams'          => 'Sales Teams',
    ],

    'dimensions' => [
        'salesperson'         => 'Salesperson',
        'team'                => 'Sales Team',
        'stage'               => 'Stage',
        'type'                => 'Type',
        'city'                => 'City',
        'country'             => 'Country',
        'lost-reason'         => 'Lost Reason',
        'company'             => 'Company',
        'campaign'            => 'Campaign',
        'medium'              => 'Medium',
        'source'              => 'Source',
        'expected-closing'    => 'Expected Closing',
        'created-at'          => 'Creation Date',
        'converted-at'        => 'Conversion Date',
        'closed-at'           => 'Closed Date',
        'activity-type'       => 'Activity Type',
        'assigned-to'         => 'Assigned To',
        'done-by'             => 'Done By',
        'done-at'             => 'Completion Date',
        'activity-created-at' => 'Activity Creation Date',
    ],

    'measures' => [
        'count'            => 'Count',
        'expected-revenue' => 'Expected Revenue',
        'prorated-revenue' => 'Prorated Revenue',
        'probability'      => 'Probability (avg.)',
        'days-to-assign'   => 'Days to Assign',
        'days-to-close'    => 'Days to Close',
    ],

    'presets' => [
        'my-pipeline'       => 'My Pipeline',
        'unassigned'        => 'Unassigned',
        'opportunities'     => 'Opportunities',
        'leads'             => 'Leads',
        'upcoming-closings' => 'Upcoming Closings',
        'active'            => 'Active',
        'ongoing'           => 'Ongoing',
        'won'               => 'Won',
        'lost'              => 'Lost',
        'created-this-year' => 'Created This Year',
        'closed-this-year'  => 'Closed This Year',
        'last-12-months'    => 'Last 12 Months',
        'my-activities'     => 'My Activities',
        'done-by-me'        => 'Done by Me',
    ],

    'table' => [
        'lost' => 'Lost',

        'columns' => [
            'name'             => 'Name',
            'type'             => 'Type',
            'customer'         => 'Customer',
            'stage'            => 'Stage',
            'expected-revenue' => 'Expected Revenue',
            'probability'      => 'Probability',
            'salesperson'      => 'Salesperson',
            'team'             => 'Sales Team',
            'expected-closing' => 'Expected Closing',
            'created-at'       => 'Created On',
            'closed-at'        => 'Closed On',
            'lost-reason'      => 'Lost Reason',
            'country'          => 'Country',
            'city'             => 'City',
            'done-at'          => 'Done On',
            'activity-type'    => 'Activity Type',
            'summary'          => 'Summary',
            'lead'             => 'Lead / Opportunity',
            'assigned-to'      => 'Assigned To',
            'done-by'          => 'Done By',
            'due-date'         => 'Due Date',
        ],
    ],

    'empty-state' => [
        'heading'     => 'No data yet',
        'description' => 'Try removing some filters.',
    ],

    'tabs' => [
        'leads'         => 'Leads',
        'opportunities' => 'Opportunities',
        'mine'          => 'Mine',
        'won'           => 'Won',
        'lost'          => 'Lost',
        'my-activities' => 'My Activities',
        'this-month'    => 'This Month',
    ],
];
