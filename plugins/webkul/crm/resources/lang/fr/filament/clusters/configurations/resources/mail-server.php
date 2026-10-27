<?php

return [
    'model-label'        => 'Serveur de messagerie entrant',
    'plural-model-label' => 'Serveurs de messagerie entrants',

    'navigation' => [
        'title' => 'Serveurs de messagerie entrants',
        'group' => 'E-mail',
    ],

    'header-actions' => [
        'create' => [
            'label'        => 'Nouveau serveur de messagerie',
            'notification' => 'Serveur de messagerie créé',
        ],
    ],

    'form' => [
        'name'                        => 'Nom',
        'name-placeholder'            => 'par ex. Boîte commerciale',
        'host'                        => 'Serveur (hôte IMAP)',
        'encryption'                  => 'Chiffrement',
        'port'                        => 'Port',
        'folder'                      => 'Dossier',
        'username'                    => 'Nom d\'utilisateur',
        'password'                    => 'Mot de passe',
        'password-helper'             => 'Laissez vide pour conserver le mot de passe actuel.',
        'validate-certificate'        => 'Valider le certificat SSL',
        'validate-certificate-helper' => 'Désactivez-le uniquement pour les serveurs avec un certificat auto-signé.',
        'active'                      => 'Actif',
        'active-helper'               => 'Consulté chaque minute pour les nouveaux e-mails.',
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nom',
            'host'         => 'Serveur',
            'folder'       => 'Dossier',
            'active'       => 'Actif',
            'last-fetched' => 'Dernière vérification',
            'last-error'   => 'Dernière erreur',
        ],

        'actions' => [
            'test' => [
                'label'   => 'Tester la connexion',
                'success' => 'Connexion réussie : :count e-mail(s) dans le dossier.',
                'failure' => 'Échec de la connexion',
            ],

            'fetch' => [
                'label'        => 'Récupérer maintenant',
                'queued'       => 'Récupération des e-mails',
                'queued-body'  => 'Les nouveaux e-mails sont lus en arrière-plan (le worker de la file doit être lancé). Voir le résultat dans E-mails reçus.',
            ],

            'edit' => [
                'notification' => 'Serveur de messagerie mis à jour',
            ],

            'delete' => [
                'notification' => 'Serveur de messagerie supprimé',
            ],
        ],

        'empty' => [
            'heading'     => 'Aucun serveur de messagerie entrant',
            'description' => 'Ajoutez la boîte qui reçoit les e-mails envoyés aux alias de vos équipes commerciales.',
        ],
    ],
];
