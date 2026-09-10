<?php

return [
    'title'         => 'Mot de passe oublié',
    'heading'       => 'Mot de passe oublié',
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
        'actions' => [
            'request' => [
                'label' => 'Envoyer le lien de réinitialisation',
            ],
        ],
    ],
    'actions' => [
        'login' => [
            'label' => 'Retour à la connexion',
        ],
    ],
];
