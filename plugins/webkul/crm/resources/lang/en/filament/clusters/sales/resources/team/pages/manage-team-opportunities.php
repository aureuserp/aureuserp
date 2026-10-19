<?php

return [
    'title' => ':team — Pipeline',

    'navigation' => [
        'title' => 'Opportunities',
    ],

    'header-actions' => [
        'kanban' => [
            'label' => 'Kanban',
        ],

        'create' => [
            'label' => 'New Opportunity',

            'notification' => [
                'title' => 'Opportunity created',
                'body'  => 'The opportunity has been added to this team\'s pipeline.',
            ],
        ],
    ],
];
