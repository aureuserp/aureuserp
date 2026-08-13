<?php

return [
    'title' => 'Facture',

    'navigation' => [
        'title' => 'Factures',
        'group' => 'Factures',
    ],

    'global-search' => [
        'customer' => 'Client',
        'date'     => 'Date',
        'due-date' => 'Date d\'échéance',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Général',

                'fields' => [
                    'customer-invoice' => 'Facture client',
                    'customer'         => 'Client',
                    'invoice-date'     => 'Date de facture',
                    'due-date'         => 'Date d\'échéance',
                    'payment-term'     => 'Condition de paiement',
                    'journal'          => 'Journal',
                    'currency'         => 'Devise',
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
                            'discount-percentage' => 'Remise',
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
                    'invoice' => [
                        'title'  => 'Facture',

                        'fields' => [
                            'customer-reference' => 'Référence client',
                            'sales-person'       => 'Vendeur',
                            'payment-reference'  => 'Référence de paiement',
                            'recipient-bank'     => 'Banque du bénéficiaire',
                            'delivery-date'      => 'Date de livraison',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Comptabilité',

                        'fields' => [
                            'company'                 => 'Société',
                            'incoterm'                => 'Incoterm',
                            'incoterm-location'       => 'Lieu de l\'incoterm',
                            'fiscal-position'         => 'Position fiscale',
                            'fiscal-position-tooltip' => 'Les positions fiscales sont utilisées pour adapter les taxes et les comptes en fonction de la localisation du client.',
                            'cash-rounding'           => 'Méthode d\'arrondi de caisse',
                            'cash-rounding-tooltip'   => 'Spécifie la plus petite unité de paiement en espèces de la devise.',
                            'payment-method'          => 'Mode de paiement',
                            'auto-post'               => 'Comptabilisation automatique',
                            'checked'                 => 'Vérifiée',
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
            'created-by'       => 'Créé par',
            'customer'         => 'Client',
            'invoice-date'     => 'Date de facture',
            'checked'          => 'Vérifiée',
            'accounting-date'  => 'Comptabilité',
            'due-date'         => 'Date d\'échéance',
            'source-document'  => 'Document source',
            'reference'        => 'Référence',
            'sales-person'     => 'Vendeur',
            'tax-excluded'     => 'Hors taxe',
            'tax'              => 'Taxe',
            'total'            => 'Total',
            'amount-due'       => 'Montant dû',
            'invoice-currency' => 'Devise de la facture',
        ],

        'summarizers' => [
            'total' => 'Total',
        ],

        'groups' => [
            'name'                         => 'Nom',
            'invoice-partner-display-name' => 'Nom d\'affichage du partenaire de la facture',
            'invoice-date'                 => 'Date de facture',
            'checked'                      => 'Vérifiée',
            'date'                         => 'Date',
            'invoice-due-date'             => 'Date d\'échéance de la facture',
            'invoice-origin'               => 'Origine de la facture',
            'sales-person'                 => 'Vendeur',
            'currency'                     => 'Devise',
            'created-at'                   => 'Créé le',
            'updated-at'                   => 'Mis à jour le',
        ],

        'filters' => [
            'number'                       => 'Numéro',
            'invoice-partner-display-name' => 'Nom d\'affichage du partenaire de la facture',
            'invoice-date'                 => 'Date de facture',
            'invoice-due-date'             => 'Date d\'échéance de la facture',
            'invoice-origin'               => 'Origine de la facture',
            'reference'                    => 'Référence',
            'payment-reference'            => 'Référence de paiement',
            'narration'                    => 'Narration',
            'partner'                      => 'Partenaire',
            'journal'                      => 'Journal',
            'fiscal-position'              => 'Position fiscale',
            'currency'                     => 'Devise',
            'company'                      => 'Société',
            'date'                         => 'Date comptable',
            'delivery-date'                => 'Date de livraison',
            'amount-untaxed'               => 'Montant hors taxe',
            'amount-tax'                   => 'Montant de la taxe',
            'amount-total'                 => 'Montant total',
            'amount-residual'              => 'Montant dû',
            'checked'                      => 'Vérifiée',
            'posted-before'                => 'Comptabilisée avant',
            'is-move-sent'                 => 'Envoyée',
            'created-at'                   => 'Créé le',
            'updated-at'                   => 'Mis à jour le',
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
                    'customer-invoice' => 'Facture client',
                    'customer'         => 'Client',
                    'invoice-date'     => 'Date de facture',
                    'due-date'         => 'Date d\'échéance',
                    'payment-term'     => 'Condition de paiement',
                    'journal'          => 'Journal',
                    'currency'         => 'Devise',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Lignes de facture',

                'repeater' => [
                    'products' => [
                        'entries' => [
                            'product'             => 'Produit',
                            'quantity'            => 'Quantité',
                            'unit'                => 'Unité de mesure',
                            'taxes'               => 'Taxes',
                            'discount-percentage' => 'Pourcentage de remise',
                            'unit-price'          => 'Prix unitaire',
                            'sub-total'           => 'Sous-total',
                            'total'               => 'Total',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Autres informations',

                'fieldset' => [
                    'invoice' => [
                        'title'   => 'Facture',

                        'entries' => [
                            'customer-reference' => 'Référence client',
                            'sales-person'       => 'Vendeur',
                            'payment-reference'  => 'Référence de paiement',
                            'recipient-bank'     => 'Banque du bénéficiaire',
                            'delivery-date'      => 'Date de livraison',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Comptabilité',

                        'entries' => [
                            'company'           => 'Société',
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Lieu de l\'incoterm',
                            'payment-method'    => 'Mode de paiement',
                            'cash-rounding'     => 'Méthode d\'arrondi de caisse',
                            'fiscal-position'   => 'Position fiscale',
                            'auto-post'         => 'Comptabilisation automatique',
                            'checked'           => 'Vérifiée',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Conditions générales',
            ],

            'journal-items' => [
                'title' => 'Écritures du journal',

                'repeater' => [
                    'entries' => [
                        'account'  => 'Compte',
                        'partner'  => 'Partenaire',
                        'label'    => 'Libellé',
                        'currency' => 'Devise',
                        'due-date' => 'Date d\'échéance',
                        'taxes'    => 'Taxes',
                        'debit'    => 'Débit',
                        'credit'   => 'Crédit',
                    ],
                ],
            ],
        ],
    ],

    'summary' => [
        'actions' => [
            'reconcile' => [
                'label' => 'Ajouter',
            ],

            'unreconcile' => [
                'label' => 'Dissocier',
            ],
        ],
    ],

];
