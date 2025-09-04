<?php

return [
    'title'    => 'Login',
    'heading'  => 'Login',
    'messages' => [
        'failed' => 'These credentials do not match our records.',
    ],
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
        'password' => [
            'label' => 'Password',
        ],
        'remember' => [
            'label' => 'Remember me',
        ],
        'actions' => [
            'authenticate' => [
                'label' => 'Sign in',
            ],
        ],
    ],
    'actions' => [
        'register' => [
            'before' => 'Don\'t have an account?',
            'label'  => 'Create account',
        ],
        'request_password_reset' => [
            'label' => 'Forgot password?',
        ],
    ],
];
