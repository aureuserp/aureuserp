<?php

return [
    'navigation' => [
        'title' => 'Comptes bancaires',
        'group' => 'Banques',
    ],

    'form' => [
        'account-number'     => 'Numéro de compte',
        'bank'               => [
            'title'    => 'Banque',
            'sections' => [
                'general' => [
                    'title' => 'Général',

                    'fields' => [
                        'name'  => 'Nom',
                        'code'  => 'Code d\'identification bancaire',
                        'email' => 'E-mail',
                        'phone' => 'Téléphone',
                    ],
                ],

                'address' => [
                    'title' => 'Adresse',

                    'fields' => [
                        'address' => 'Adresse',
                        'city'    => 'Ville',
                        'street1' => 'Rue 1',
                        'street2' => 'Rue 2',
                        'state'   => 'État',
                        'zip'     => 'Code postal',
                        'country' => 'Pays',
                    ],
                ],
            ],
        ],

        'account-holder'     => 'Titulaire du compte',
    ],

    'table' => [
        'columns' => [
            'account-number' => 'Numéro de compte',
            'bank'           => 'Banque',
            'account-holder' => 'Titulaire du compte',
            'send-money'     => 'Peut envoyer de l\'argent',
            'created-at'     => 'Créé le',
            'updated-at'     => 'Mis à jour le',
            'deleted-at'     => 'Supprimé le',
        ],

        'filters' => [
            'bank'           => 'Banque',
            'account-holder' => 'Titulaire du compte',
            'creator'        => 'Créateur',
            'can-send-money' => 'Peut envoyer de l\'argent',
        ],

        'groups' => [
            'bank'               => 'Banque',
            'can-send-money'     => 'Peut envoyer de l\'argent',
            'created-at'         => 'Créé le',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Compte bancaire mis à jour',
                    'body'  => 'Le compte bancaire a été mis à jour avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Compte bancaire restauré',
                    'body'  => 'Le compte bancaire a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Compte bancaire supprimé',
                    'body'  => 'Le compte bancaire a été supprimé avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Compte bancaire supprimé définitivement',
                    'body'  => 'Le compte bancaire a été supprimé définitivement avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Comptes bancaires restaurés',
                    'body'  => 'Les comptes bancaires ont été restaurés avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Comptes bancaires supprimés',
                    'body'  => 'Les comptes bancaires ont été supprimés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Comptes bancaires supprimés définitivement',
                    'body'  => 'Les comptes bancaires ont été supprimés définitivement avec succès.',
                ],
            ],
        ],
    ],
];
