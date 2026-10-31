<?php

return [
    'model-label'        => 'Level',
    'plural-model-label' => 'Levels',

    'navigation' => [
        'title' => 'Levels',
        'group' => 'Members',
    ],

    'header-actions' => [
        'create' => 'New Level',
    ],

    'form' => [
        'name'          => 'Name',
        'weight'        => 'Level Weight',
        'weight-helper' => 'How likely partners of this level are to be given leads. 0 means no leads.',
    ],

    'table' => [
        'columns' => [
            'name'     => 'Name',
            'weight'   => 'Level Weight',
            'partners' => 'Contacts',
        ],
    ],

    'tabs' => [
        'active'   => 'Active',
        'archived' => 'Archived',
    ],

    'notifications' => [
        'created'  => 'Level created',
        'updated'  => 'Level updated',
        'restored' => 'Level restored',
        'deleted'  => 'Level deleted',
    ],

    'contact' => [
        'section'       => 'Partner level',
        'level'         => 'Level',
        'weight'        => 'Level Weight',
        'weight-helper' => 'Leave empty to use the level\'s weight.',
    ],
];
