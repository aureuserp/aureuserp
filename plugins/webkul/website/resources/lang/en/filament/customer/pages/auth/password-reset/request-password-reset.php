<?php

return [
    'title'         => 'Forgot password',
    'heading'       => 'Forgot password',
    'notifications' => [
        'throttled' => [
            'title' => 'Too many attempts. Try again in :seconds seconds.',
            'body'  => 'Please wait :seconds seconds (:minutes minutes) before trying again.',
        ],
    ],
    'form' => [
        'email' => [
            'label' => 'Email address',
        ],
        'actions' => [
            'request' => [
                'label' => 'Send reset link',
            ],
        ],
    ],
    'actions' => [
        'login' => [
            'label' => 'Back to login',
        ],
    ],
];
