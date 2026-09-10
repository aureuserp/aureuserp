<?php

return [
    'title'         => 'Inscription',
    'heading'       => 'Inscription',
    'notifications' => [
        'throttled' => [
            'title' => 'Trop de tentatives. Réessayez dans :seconds secondes.',
            'body'  => 'Veuillez patienter :seconds secondes (:minutes minutes) avant de réessayer.',
        ],
    ],
    'form' => [
        'name' => [
            'label' => 'Nom',
        ],
        'email' => [
            'label' => 'Adresse email',
        ],
        'password' => [
            'label'                => 'Mot de passe',
            'validation_attribute' => 'mot de passe',
        ],
        'password_confirmation' => [
            'label' => 'Confirmer le mot de passe',
        ],
        'actions' => [
            'register' => [
                'label' => 'Créer un compte',
            ],
        ],
    ],
    'actions' => [
        'login' => [
            'before' => 'Vous avez déjà un compte ?',
            'label'  => 'Se connecter',
        ],
    ],
];
