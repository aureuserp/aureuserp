<?php

return [
    'title' => 'Paiement',

    'navigation' => [
        'title' => 'Paiements',
        'group' => 'Factures',
    ],

    'global-search' => [
        'partner' => 'Partenaire',
        'amount'  => 'Montant',
        'date'    => 'Date',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'payment-type'          => 'Type de paiement',
                'memo'                  => 'Mémo',
                'date'                  => 'Date',
                'amount'                => 'Montant',
                'currency'              => 'Devise',
                'payment-method'        => 'Mode de paiement',
                'customer'              => 'Client',
                'vendor'                => 'Fournisseur',
                'journal'               => 'Journal',
                'customer-bank-account' => 'Compte bancaire du client',
                'vendor-bank-account'   => 'Compte bancaire du fournisseur',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Nom',
            'date'            => 'Date',
            'journal'         => 'Journal',
            'payment-method'  => 'Mode de paiement',
            'partner'         => 'Partenaire',
            'amount-currency' => 'Montant (devise)',
            'amount'          => 'Montant',
            'state'           => 'État',
            'company'         => 'Société',
            'currency'        => 'Devise',
            'created-by'      => 'Créé par',
        ],

        'groups' => [
            'name'                             => 'Nom',
            'company'                          => 'Société',
            'journal'                          => 'Journal',
            'partner'                          => 'Partenaire',
            'payment-method-line'              => 'Ligne de mode de paiement',
            'payment-method'                   => 'Mode de paiement',
            'partner-bank-account'             => 'Compte bancaire du partenaire',
            'created-at'                       => 'Créé le',
            'updated-at'                       => 'Mis à jour le',
        ],

        'filters' => [
            'company'                          => 'Société',
            'journal'                          => 'Journal',
            'customer-bank-account'            => 'Compte bancaire du client',
            'payment-method'                   => 'Mode de paiement',
            'currency'                         => 'Devise',
            'partner'                          => 'Partenaire',
            'payment-method-line'              => 'Ligne de mode de paiement',
            'created-at'                       => 'Créé le',
            'updated-at'                       => 'Mis à jour le',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Paiement supprimé',
                    'body'  => 'Le paiement a été supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Paiements supprimés',
                    'body'  => 'Les paiements ont été supprimés avec succès.',
                ],
            ],
        ],

        'toolbar-actions' => [
            'export' => [
                'label' => 'Exporter',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'payment-information' => [
                'title'   => 'Informations de paiement',
                'entries' => [
                    'state'                 => 'État',
                    'vendor'                => 'Fournisseur',
                    'customer'              => 'Client',
                    'payment-type'          => 'Type de paiement',
                    'journal'               => 'Journal',
                    'customer-bank-account' => 'Compte bancaire du client',
                    'vendor-bank-account'   => 'Compte bancaire du fournisseur',
                    'amount'                => 'Montant',
                    'payment-method'        => 'Mode de paiement',
                    'date'                  => 'Date',
                    'memo'                  => 'Mémo',
                ],
            ],
        ],
    ],

];
