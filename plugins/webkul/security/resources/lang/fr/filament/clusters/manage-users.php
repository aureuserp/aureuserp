<?php

return [
    'breadcrumb' => 'Gérer les utilisateurs',
    'title'      => 'Gérer les utilisateurs',
    'group'      => 'Général',

    'navigation' => [
        'label' => 'Gérer les utilisateurs',
    ],

    'form' => [
        'enable-user-invitation' => [
            'label'       => 'Activer l’invitation des utilisateurs',
            'helper-text' => 'Autoriser les utilisateurs à inviter d’autres utilisateurs dans l’application.',
        ],

        'enable-reset-password' => [
            'label'       => 'Activer la réinitialisation du mot de passe',
            'helper-text' => 'Autoriser les utilisateurs à réinitialiser leur mot de passe.',
        ],

        'default-role' => [
            'label'       => 'Rôle par défaut',
            'helper-text' => 'Le rôle par défaut attribué aux nouveaux utilisateurs.',
        ],

        'default-company' => [
            'label'       => 'Société par défaut',
            'helper-text' => 'La société par défaut attribuée aux nouveaux utilisateurs.',
        ],
    ],
];
