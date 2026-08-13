<?php

return [
    'tabs' => [
        'all'      => 'Tous les utilisateurs',
        'archived' => 'Utilisateurs archivés',
    ],

    'header-actions' => [
        'invite' => [
            'title' => 'Inviter un utilisateur',
            'modal' => [
                'submit-action-label' => 'Inviter un utilisateur',
            ],
            'form' => [
                'email' => 'E-mail',
            ],
            'notification' => [
                'success' => [
                    'title' => 'Utilisateur invité',
                    'body'  => 'L’utilisateur a été invité avec succès',
                ],
                'error' => [
                    'title' => 'Échec de l’invitation de l’utilisateur',
                    'body'  => 'Le système a rencontré une erreur inattendue lors de l’envoi de l’invitation de l’utilisateur.',
                ],

                'default-company-error' => [
                    'title' => 'Société par défaut non définie',
                    'body'  => 'Veuillez définir la société par défaut dans les paramètres avant d’inviter un utilisateur.',
                ],
            ],
        ],

        'create' => [
            'label' => 'Nouvel utilisateur',
        ],
    ],
];
