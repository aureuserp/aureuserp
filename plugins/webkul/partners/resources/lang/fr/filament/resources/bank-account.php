<?php

return [
    'navigation' => [
        'group' => 'Banques',
        'title' => 'Comptes bancaires',
    ],

    'form' => [
        'account-number'     => 'Numéro de compte',
        'bank'               => 'Banque',
        'account-holder'     => 'Titulaire du compte',
        'can-send-money'     => 'Peut envoyer de l\'argent',
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
                    'title' => 'Compte bancaire définitivement supprimé',
                    'body'  => 'Le compte bancaire a été définitivement supprimé avec succès.',
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
                    'title' => 'Comptes bancaires définitivement supprimés',
                    'body'  => 'Les comptes bancaires ont été définitivement supprimés avec succès.',
                ],
            ],
        ],
    ],
];
