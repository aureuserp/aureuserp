<?php

return [
    'model-label'        => 'Sales Team',
    'plural-model-label' => 'Sales Teams',

    'navigation' => [
        'title' => 'Teams',
    ],

    'table' => [
        'columns' => [
            'name'               => 'Sales Team',
            'open-opportunities' => 'Open Opportunities',
            'expected-revenue'   => 'Expected Revenue',
            'members'            => 'Members',
            'company'            => 'Company',
        ],

        'filters' => [
            'team-leader' => 'Team Leader',
            'created-at'  => 'Created At',
        ],

        'actions' => [
            'pipeline' => [
                'label' => 'Pipeline',
            ],
        ],
    ],
];
