<?php

return [
    'title'         => 'Réinitialiser le mot de passe',
    'heading'       => 'Réinitialiser le mot de passe',
    'notifications' => [
        'throttled' => [
            'title' => 'Trop de tentatives. Réessayez dans :seconds secondes.',
            'body'  => 'Veuillez patienter :seconds secondes (:minutes minutes) avant de réessayer.',
        ],
    ],
    'form' => [
        'email' => [
            'label' => 'Adresse email',
        ],
        'password' => [
            'label'                => 'Nouveau mot de passe',
            'validation_attribute' => 'mot de passe',
        ],
        'password_confirmation' => [
            'label' => 'Confirmer le nouveau mot de passe',
        ],
        'actions' => [
            'reset' => [
                'label' => 'Réinitialiser le mot de passe',
            ],
        ],
    ],
];
