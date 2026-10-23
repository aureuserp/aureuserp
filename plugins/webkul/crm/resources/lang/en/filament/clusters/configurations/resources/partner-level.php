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

    'actions' => [
        'archive'             => 'Archive',
        'archive-heading'     => 'Archive level',
        'archive-description' => 'The level is moved to Archived. Contacts keep it; restore it from the Archived tab.',
    ],

    'tabs' => [
        'active'   => 'Active',
        'archived' => 'Archived',
    ],

    'notifications' => [
        'created'  => 'Level created',
        'updated'  => 'Level updated',
        'archived' => 'Level archived',
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
