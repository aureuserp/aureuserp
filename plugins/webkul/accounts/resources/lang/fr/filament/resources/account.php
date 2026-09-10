<?php

return [
    'global-search' => [
        'code' => 'Code',
        'type' => 'Type',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'code'                   => 'Code',
                'account-name'           => 'Nom du compte',
                'accounting'             => 'Comptabilité',
                'account-type'           => 'Type de compte',
                'parent-account'         => 'Compte parent',
                'parent-account-helper'  => 'Sélectionnez un compte existant pour en faire un sous-compte.',
                'default-taxes'          => 'Taxes par défaut',
                'tags'                   => 'Étiquettes',
                'journals'               => 'Journaux',
                'journals-helper'        => 'Suggéré automatiquement en fonction du type de compte sélectionné. Vous pouvez modifier la sélection.',
                'currency'               => 'Devise',
                'deprecated'             => 'Obsolète',
                'reconcile'              => 'Autoriser le lettrage',
                'non-trade'              => 'Hors commerce',
                'companies'              => 'Sociétés',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'           => 'Code',
            'account-name'   => 'Nom du compte',
            'account-type'   => 'Compte',
            'parent-account' => 'Compte parent',
            'currency'       => 'Devise',
            'journals'       => 'Journaux',
            'reconcile'      => 'Autoriser le lettrage',
        ],

        'grouping' => [
            'account-type' => 'Type de compte',
        ],

        'filters' => [
            'account-type'     => 'Type de compte',
            'parent-account'   => 'Compte parent',
            'allow-reconcile'  => 'Autoriser le lettrage',
            'currency'         => 'Devise',
            'account-journals' => 'Journaux',
            'non-trade'        => 'Hors commerce',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Compte mis à jour',
                    'body'  => 'Le compte a été mis à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Compte supprimé',
                        'body'  => 'Le compte a été supprimé avec succès.',
                    ],

                    'error' => [
                        'title' => 'Échec de la suppression du compte',
                        'body'  => 'Le compte n\'a pas pu être supprimé car il possède des écritures comptables associées.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Comptes supprimés',
                        'body'  => 'Les comptes ont été supprimés avec succès.',
                    ],

                    'error' => [
                        'title' => 'Échec de la suppression des comptes',
                        'body'  => 'Les comptes n\'ont pas pu être supprimés car ils possèdent des écritures comptables associées.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'code'           => 'Code',
                'account-name'   => 'Nom du compte',
                'accounting'     => 'Comptabilité',
                'account-type'   => 'Type de compte',
                'parent-account' => 'Compte parent',
                'sub-accounts'   => 'Sous-comptes',
                'default-taxes'  => 'Taxes par défaut',
                'tags'           => 'Étiquettes',
                'journals'       => 'Journaux',
                'currency'       => 'Devise',
                'deprecated'     => 'Obsolète',
                'reconcile'      => 'Lettrage',
                'non-trade'      => 'Hors commerce',
            ],
        ],
    ],
];
