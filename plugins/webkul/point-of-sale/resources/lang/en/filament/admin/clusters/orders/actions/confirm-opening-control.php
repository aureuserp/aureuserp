<?php

return [
    'label' => 'Open Session',

    'form' => [
        'fields' => [
            'cash-balance-start' => 'Opening Cash Balance',
            'opening-notes'      => 'Notes',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Session opened',
            'body'  => 'The session is now in progress.',
        ],
    ],
];
