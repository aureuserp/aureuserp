<?php

return [
    'title'    => 'Connexion',
    'heading'  => 'Connexion',
    'messages' => [
        'failed' => 'Ces identifiants ne correspondent pas à nos enregistrements.',
    ],
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
            'label' => 'Mot de passe',
        ],
        'remember' => [
            'label' => 'Se souvenir de moi',
        ],
        'actions' => [
            'authenticate' => [
                'label' => 'Se connecter',
            ],
        ],
    ],
    'actions' => [
        'register' => [
            'before' => 'Vous n\'avez pas de compte ?',
            'label'  => 'Créer un compte',
        ],
        'request_password_reset' => [
            'label' => 'Mot de passe oublié ?',
        ],
    ],
];
