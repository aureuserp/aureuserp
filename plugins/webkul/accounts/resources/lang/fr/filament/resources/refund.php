<?php

return [
    'title' => 'Facture',

    'navigation' => [
        'title' => 'Factures',
        'group' => 'Factures',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Général',
                'fields' => [
                    'vendor-credit-note' => 'Avoir fournisseur',
                    'vendor'             => 'Fournisseur',
                    'bill-date'          => 'Date de facture',
                    'bill-reference'     => 'Référence de facture',
                    'accounting-date'    => 'Date comptable',
                    'payment-reference'  => 'Référence de paiement',
                    'recipient-bank'     => 'Banque du destinataire',
                    'due-date'           => 'Date d\'échéance',
                    'payment-term'       => 'Condition de paiement',
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
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Lieu de l\'incoterm',
                        ],
                    ],

                    'secured' => [
                        'title'  => 'Sécurisé',
                        'fields' => [
                            'payment-method' => 'Mode de paiement',
                            'auto-post'      => 'Comptabilisation automatique',
                            'checked'        => 'Vérifié',
                        ],
                    ],

                    'additional-information' => [
                        'title'  => 'Informations complémentaires',
                        'fields' => [
                            'company'  => 'Société',
                            'currency' => 'Devise',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Conditions générales',
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
                    'bill-date'         => 'Date de facture',
                    'bill-reference'    => 'Référence de facture',
                    'accounting-date'   => 'Date comptable',
                    'payment-reference' => 'Référence de paiement',
                    'recipient-bank'    => 'Banque du destinataire',
                    'due-date'          => 'Date d\'échéance',
                    'payment-term'      => 'Condition de paiement',
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
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Lieu de l\'incoterm',
                        ],
                    ],

                    'secured' => [
                        'title'   => 'Sécurisé',
                        'entries' => [
                            'payment-method' => 'Mode de paiement',
                            'auto-post'      => 'Comptabilisation automatique',
                            'checked'        => 'Vérifié',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'Informations complémentaires',
                        'entries' => [
                            'company'  => 'Société',
                            'currency' => 'Devise',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Conditions générales',
            ],
        ],
    ],
];
