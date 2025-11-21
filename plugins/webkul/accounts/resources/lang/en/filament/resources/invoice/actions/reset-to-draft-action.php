<?php

return [
    'title' => 'Reset To Draft',

    'validation' => [
        'notification' => [
            'error' => [
                'invalid-state' => [
                    'title' => 'Journal Entry State Invalid',
                    'body'  => 'Only posted or cancelled journal entries can be reset to draft.',
                ],
            ],
        ],
    ],
];
