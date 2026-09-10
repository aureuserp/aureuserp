<?php

return [
    'title' => 'Facture',

    'navigation' => [
        'title' => 'Factures',
        'group' => 'Factures',
    ],

    'global-search' => [
        'vendor'   => 'Fournisseur',
        'date'     => 'Date',
        'due-date' => 'Date d\'échéance',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Général',
                'fields' => [
                    'vendor-bill'       => 'Facture fournisseur',
                    'vendor'            => 'Fournisseur',
                    'bill-date'         => 'Date de la facture',
                    'bill-reference'    => 'Référence de la facture',
                    'accounting-date'   => 'Date comptable',
                    'payment-reference' => 'Référence de paiement',
                    'recipient-bank'    => 'Banque du bénéficiaire',
                    'due-date'          => 'Date d\'échéance',
                    'payment-term'      => 'Condition de paiement',
                    'journal'           => 'Journal',
                    'currency'          => 'Devise',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Lignes de facture',

                'repeater' => [
                    'products' => [
                        'title'       => 'Produits',
                        'add-product' => 'Ajouter un produit',

                        'columns' => [
                            'product'             => 'Produit',
                            'quantity'            => 'Quantité',
                            'unit'                => 'Unité',
                            'taxes'               => 'Taxes',
                            'discount-percentage' => 'Pourcentage de remise',
                            'unit-price'          => 'Prix unitaire',
                            'sub-total'           => 'Sous-total',
                        ],

                        'fields' => [
                            'product'             => 'Produit',
                            'quantity'            => 'Quantité',
                            'unit'                => 'Unité',
                            'taxes'               => 'Taxes',
                            'discount-percentage' => 'Pourcentage de remise',
                            'unit-price'          => 'Prix unitaire',
                            'sub-total'           => 'Sous-total',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Autres informations',

                'fieldset' => [
                    'accounting' => [
                        'title' => 'Comptabilité',

                        'fields' => [
                            'company'                 => 'Société',
                            'incoterm'                => 'Incoterm',
                            'incoterm-location'       => 'Lieu de l\'incoterm',
                            'payment-method'          => 'Mode de paiement',
                            'fiscal-position'         => 'Position fiscale',
                            'fiscal-position-tooltip' => 'Les positions fiscales permettent d\'adapter les taxes et les comptes en fonction de la localisation du client.',
                            'cash-rounding'           => 'Méthode d\'arrondi de caisse',
                            'cash-rounding-tooltip'   => 'Spécifie la plus petite unité de paiement en espèces de la devise.',
                            'auto-post'               => 'Comptabilisation automatique',
                            'checked'                 => 'Vérifié',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Conditions générales',
            ],
        ],
    ],

    'table' => [
        'total'   => 'Total',
        'columns' => [
            'number'           => 'Numéro',
            'state'            => 'État',
            'customer'         => 'Client',
            'bill-date'        => 'Date de la facture',
            'checked'          => 'Vérifié',
            'accounting-date'  => 'Comptabilité',
            'due-date'         => 'Date d\'échéance',
            'source-document'  => 'Document source',
            'reference'        => 'Référence',
            'sales-person'     => 'Vendeur',
            'tax-excluded'     => 'Hors taxe',
            'tax'              => 'Taxe',
            'total'            => 'Total',
            'amount-due'       => 'Montant dû',
            'bill-currency'    => 'Devise de la facture',
        ],

        'summarizers' => [
            'total' => 'Total',
        ],

        'groups' => [
            'name'                         => 'Nom',
            'bill-partner-display-name'    => 'Nom affiché du partenaire de la facture',
            'bill-date'                    => 'Date de la facture',
            'checked'                      => 'Vérifié',
            'date'                         => 'Date',
            'bill-due-date'                => 'Date d\'échéance de la facture',
            'bill-origin'                  => 'Origine de la facture',
            'sales-person'                 => 'Vendeur',
            'currency'                     => 'Devise',
            'created-at'                   => 'Créé le',
            'updated-at'                   => 'Mis à jour le',
        ],

        'filters' => [
            'number'                    => 'Numéro',
            'bill-partner-display-name' => 'Nom affiché du partenaire de la facture',
            'bill-date'                 => 'Date de la facture',
            'bill-due-date'             => 'Date d\'échéance de la facture',
            'bill-origin'               => 'Origine de la facture',
            'reference'                 => 'Référence',
            'payment-reference'         => 'Référence de paiement',
            'narration'                 => 'Note',
            'partner'                   => 'Partenaire',
            'journal'                   => 'Journal',
            'fiscal-position'           => 'Position fiscale',
            'currency'                  => 'Devise',
            'company'                   => 'Société',
            'date'                      => 'Date comptable',
            'delivery-date'             => 'Date de livraison',
            'amount-untaxed'            => 'Montant hors taxe',
            'amount-tax'                => 'Montant de la taxe',
            'amount-total'              => 'Montant total',
            'amount-residual'           => 'Montant dû',
            'checked'                   => 'Vérifié',
            'posted-before'             => 'Comptabilisé avant',
            'is-move-sent'              => 'Envoyé',
            'created-at'                => 'Créé le',
            'updated-at'                => 'Mis à jour le',
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
        'section' => [
            'general' => [
                'title'   => 'Général',

                'entries' => [
                    'vendor-invoice'    => 'Facture fournisseur',
                    'vendor'            => 'Fournisseur',
                    'bill-date'         => 'Date de la facture',
                    'bill-reference'    => 'Référence de la facture',
                    'accounting-date'   => 'Date comptable',
                    'payment-reference' => 'Référence de paiement',
                    'recipient-bank'    => 'Banque du bénéficiaire',
                    'due-date'          => 'Date d\'échéance',
                    'payment-term'      => 'Condition de paiement',
                    'journal'           => 'Journal',
                    'currency'          => 'Devise',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Lignes de facture',

                'repeater' => [
                    'products' => [
                        'title'       => 'Produits',
                        'add-product' => 'Ajouter un produit',

                        'entries' => [
                            'product'             => 'Produit',
                            'quantity'            => 'Quantité',
                            'unit'                => 'Unité',
                            'taxes'               => 'Taxes',
                            'discount-percentage' => 'Pourcentage de remise',
                            'unit-price'          => 'Prix unitaire',
                            'sub-total'           => 'Sous-total',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Autres informations',
                'fieldset' => [
                    'accounting' => [
                        'title' => 'Comptabilité',

                        'entries' => [
                            'company'           => 'Société',
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Lieu de l\'incoterm',
                            'payment-method'    => 'Mode de paiement',
                            'checked'           => 'Vérifié',
                            'fiscal-position'   => 'Position fiscale',
                            'cash-rounding'     => 'Méthode d\'arrondi de caisse',
                            'checked'           => 'Vérifié',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Conditions générales',
            ],

            'journal-items' => [
                'title' => 'Écritures comptables',

                'repeater' => [
                    'entries' => [
                        'account'  => 'Compte',
                        'partner'  => 'Partenaire',
                        'label'    => 'Libellé',
                        'due-date' => 'Date d\'échéance',
                        'currency' => 'Devise',
                        'taxes'    => 'Taxes',
                        'debit'    => 'Débit',
                        'credit'   => 'Crédit',
                    ],
                ],
            ],
        ],
    ],
];
