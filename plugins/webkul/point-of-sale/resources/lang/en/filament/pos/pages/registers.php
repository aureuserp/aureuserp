<?php

return [
    'title' => 'Registers',

    'navigation' => [
        'label' => 'Registers',
    ],

    'session' => [
        'open'   => 'Open by :name',
        'closed' => 'Closed',
    ],

    'actions' => [
        'open'   => 'Open register',
        'resume' => 'Resume',

        'discard' => [
            'label'        => 'Discard session',
            'heading'      => 'Discard this session?',
            'description'  => 'The register was opened but nothing has been sold on it. Discarding removes the session so it can be opened again cleanly.',
            'confirm'      => 'Discard',

            'notification' => [
                'title' => 'Session discarded',
            ],
        ],
    ],

    'empty' => [
        'heading'     => 'No point of sale available',
        'description' => 'Create an active point of sale in the back office to start selling.',
    ],
];
