<?php

return [
    'model-label'        => 'Sales Team',
    'plural-model-label' => 'Sales Teams',

    'navigation' => [
        'title' => 'Sales Teams',
    ],

    'pages' => [
        'assignment' => [
            'navigation' => 'Assignment Rules',
            'title'      => ':team — Assignment Rules',
        ],

        'alias' => [
            'navigation' => 'Email Alias',
            'title'      => ':team — Email Alias',
        ],

        'members' => [
            'title'              => ':team — Members',
            'inactive'           => 'Inactive',
            'skipped'            => 'Skips auto assignment',
            'rules'              => '{1} :count rule|[0,*] :count rules',
            'lead-limit'         => 'Lead limit (30 days): :assigned / :max',
            'lead-limit-tooltip' => ':assigned of :max leads assigned in the last 30 days',
        ],
    ],
];
