<?php

return [
    'group' => [
        'label' => 'Accès au portail',
    ],

    'grant' => [
        'label' => 'Accorder l\'accès au portail',

        'modal' => [
            'heading'     => 'Accorder l\'accès au portail',
            'description' => 'Un email d\'invitation sera envoyé à :email avec un lien pour définir un mot de passe pour le portail client.',
        ],

        'notification' => [
            'email-missing' => [
                'title' => 'Adresse email requise',
                'body'  => 'Ajoutez une adresse email à ce contact avant d\'accorder l\'accès au portail.',
            ],

            'email-taken' => [
                'title' => 'Adresse email déjà utilisée',
                'body'  => 'Un autre contact ayant accès au portail utilise déjà cette adresse email.',
            ],

            'granted' => [
                'title' => 'Accès au portail accordé',
                'body'  => 'Une invitation à définir un mot de passe a été envoyée à :email.',
            ],

            'email-failed' => [
                'title' => 'Accès au portail accordé, mais l\'email d\'invitation n\'a pas pu être envoyé',
            ],
        ],
    ],

    'change-password' => [
        'label' => 'Changer le mot de passe',

        'modal' => [
            'heading'     => 'Changer le mot de passe du portail',
            'description' => 'Définissez un nouveau mot de passe du portail client pour :email.',
        ],

        'form' => [
            'password' => [
                'label' => 'Nouveau mot de passe',
            ],

            'password-confirmation' => [
                'label' => 'Confirmer le nouveau mot de passe',
            ],
        ],

        'notification' => [
            'changed' => [
                'title' => 'Mot de passe du portail modifié',
                'body'  => 'Le contact peut désormais se connecter au portail client avec le nouveau mot de passe.',
            ],
        ],
    ],

    'password-reset' => [
        'label' => 'Envoyer la réinitialisation du mot de passe',

        'modal' => [
            'heading'     => 'Envoyer la réinitialisation du mot de passe',
            'description' => 'Un lien de réinitialisation du mot de passe sera envoyé à :email.',
        ],

        'notification' => [
            'sent' => [
                'title' => 'Lien de réinitialisation du mot de passe envoyé',
                'body'  => 'Un lien de réinitialisation du mot de passe a été envoyé à :email.',
            ],

            'failed' => [
                'title' => 'Le lien de réinitialisation du mot de passe n\'a pas pu être envoyé',
            ],
        ],
    ],

    'revoke' => [
        'label' => 'Révoquer l\'accès au portail',

        'modal' => [
            'heading'     => 'Révoquer l\'accès au portail',
            'description' => 'Le contact ne pourra plus se connecter au portail client. Tous les liens de réinitialisation de mot de passe en attente seront invalidés.',
        ],

        'notification' => [
            'revoked' => [
                'title' => 'Accès au portail révoqué',
                'body'  => 'Le contact ne peut plus se connecter au portail client.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'portal-access' => 'Accès au portail',
        ],

        'filters' => [
            'portal-access' => 'Accès au portail',
        ],
    ],

    'infolist' => [
        'section' => [
            'title' => 'Portail client',
        ],

        'entries' => [
            'status' => [
                'label'   => 'Accès au portail',
                'granted' => 'Accordé',
                'none'    => 'Non accordé',
            ],

            'email-verified-at' => [
                'label'       => 'Email vérifié le',
                'placeholder' => 'Jamais',
            ],

            'last-login-at' => [
                'label'       => 'Dernière connexion au portail',
                'placeholder' => 'Jamais',
            ],
        ],
    ],
];
