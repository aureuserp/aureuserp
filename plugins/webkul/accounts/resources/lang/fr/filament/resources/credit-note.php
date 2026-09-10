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
                    'customer-invoice' => 'Avoir client',
                    'customer'         => 'Client',
                    'invoice-date'     => 'Date de facture',
                    'due-date'         => 'Date d\'échéance',
                    'payment-term'     => 'Condition de paiement',
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
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Lieu de l\'incoterm',
                            'payment-method'    => 'Mode de paiement',
                            'auto-post'         => 'Comptabilisation automatique',
                            'checked'           => 'Vérifié',
                        ],
                    ],

                    'additional-information' => [
                        'title'  => 'Informations complémentaires',
                        'fields' => [
                            'company'  => 'Société',
                            'currency' => 'Devise',
                        ],
                    ],

                    'marketing' => [
                        'title'  => 'Marketing',
                        'fields' => [
                            'campaign' => 'Campagne',
                            'medium'   => 'Support',
                            'source'   => 'Source',
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
                    'customer-invoice' => 'Avoir client',
                    'customer'         => 'Client',
                    'invoice-date'     => 'Date de facture',
                    'due-date'         => 'Date d\'échéance',
                    'payment-term'     => 'Condition de paiement',
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

                        'fieldset' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Lieu de l\'incoterm',
                            'payment-method'    => 'Mode de paiement',
                            'auto-post'         => 'Comptabilisation automatique',
                            'checked'           => 'Vérifié',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'Informations complémentaires',
                        'entries' => [
                            'company'  => 'Société',
                            'currency' => 'Devise',
                        ],
                    ],

                    'marketing' => [
                        'title'   => 'Marketing',
                        'entries' => [
                            'campaign' => 'Campagne',
                            'medium'   => 'Support',
                            'source'   => 'Source',
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
