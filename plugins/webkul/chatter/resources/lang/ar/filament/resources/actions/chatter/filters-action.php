<?php

return [
    'tooltip' => 'Filters',

    'fields'  => [
        'search'             => 'Search',
        'search-placeholder' => 'Search messages...',
        'type'               => 'Type',
        'date'               => 'Date',
        'sort-by'            => 'Sort by',
        'pinned-only'        => 'Pinned only',
    ],
    'type-options' => [
        'all'          => 'All types',
        'note'         => 'Notes',
        'comment'      => 'Comments',
        'notification' => 'Notifications',
        'activity'     => 'Activities',
    ],
    'date-options' => [
        ''          => 'Any time',
        'today'     => 'Today',
        'yesterday' => 'Yesterday',
        'week'      => 'Last 7 days',
        'month'     => 'Last 30 days',
        'quarter'   => 'Last 3 months',
        'year'      => 'Last year',
    ],
    'sort-options' => [
        'created_at_desc' => 'Newest first',
        'created_at_asc'  => 'Oldest first',
        'updated_at_desc' => 'Recently updated',
        'priority'        => 'Priority',
    ],
    'actions' => [
        'apply' => 'Apply filters',
    ],
];
