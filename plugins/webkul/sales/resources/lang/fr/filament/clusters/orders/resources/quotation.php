<?php

return [
    'title' => 'Devis',

    'navigation' => [
        'title' => 'Devis',
    ],

    'global-search' => [
        'customer'  => 'Client',
        'reference' => 'Référence',
        'amount'    => 'Montant',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Général',
                'fields' => [
                    'customer'       => 'Client',
                    'expiration'     => 'Expiration',
                    'quotation-date' => 'Date du devis',
                    'order-date'     => 'Date de commande',
                    'payment-term'   => 'Condition de paiement',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Ligne de commande',

                'repeater' => [
                    'products' => [
                        'title'       => 'Produits',
                        'add-product' => 'Ajouter un produit',

                        'columns'     => [
                            'product'                    => 'Produit',
                            'product-variants'           => 'Variantes de produit',
                            'product-simple'              => 'Produit simple',
                            'quantity'                   => 'Quantité',
                            'insufficient-stock-tooltip' => 'Stock insuffisant pour répondre à cette demande.',
                            'uom'                        => 'UdM',
                            'lead-time'                  => 'Délai',
                            'qty-delivered'              => 'Livrée',
                            'qty-invoiced'               => 'Facturée',
                            'packaging-qty'              => 'Quantité de conditionnement',
                            'packaging'                  => 'Conditionnement',
                            'unit-price'                 => 'Prix unitaire',
                            'cost'                       => 'Coût',
                            'margin'                     => 'Marge',
                            'taxes'                      => 'Taxes',
                            'amount'                     => 'Montant',
                            'margin-percentage'          => 'Marge (%)',
                            'discount-percentage'        => 'Remise (%)',
                        ],

                        'fields'      => [
                            'product'             => 'Produit',
                            'product-variants'    => 'Variantes de produit',
                            'product-simple'      => 'Produit simple',
                            'quantity'            => 'Quantité',
                            'uom'                 => 'Unité de mesure',
                            'lead-time'           => 'Délai',
                            'qty-delivered'       => 'Quantité livrée',
                            'qty-invoiced'        => 'Quantité facturée',
                            'packaging-qty'       => 'Quantité de conditionnement',
                            'packaging'           => 'Conditionnement',
                            'unit-price'          => 'Prix unitaire',
                            'cost'                => 'Coût',
                            'margin'              => 'Marge',
                            'taxes'               => 'Taxes',
                            'amount'              => 'Montant',
                            'margin-percentage'   => 'Marge (%)',
                            'discount-percentage' => 'Remise (%)',
                        ],

                        'notifications' => [
                            'quantity-below-delivered' => [
                                'title' => 'Impossible de réduire la quantité',
                                'body'  => 'Vous ne pouvez pas réduire la quantité en dessous de la quantité livrée (:qty).',
                            ],
                        ],

                        'delete-action' => [
                            'error' => [
                                'title' => 'Impossible de supprimer le produit',
                                'body'  => 'Les produits ne peuvent pas être supprimés d\'une commande client confirmée.',
                            ],
                        ],

                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Ouvrir le produit',
                            ],
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Produits optionnels',
                        'add-product' => 'Ajouter un produit',

                        'columns' => [
                            'product'             => 'Produit',
                            'description'         => 'Description',
                            'quantity'            => 'Quantité',
                            'uom'                 => 'Unité de mesure',
                            'unit-price'          => 'Prix unitaire',
                            'discount-percentage' => 'Remise (%)',
                        ],

                        'fields'      => [
                            'product'             => 'Produit',
                            'description'         => 'Description',
                            'quantity'            => 'Quantité',
                            'uom'                 => 'Unité de mesure',
                            'unit-price'          => 'Prix unitaire',
                            'discount-percentage' => 'Remise (%)',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Ajouter une ligne de commande',
                                    'already-added'  => 'Déjà ajouté à la commande',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Produit ajouté',
                                        'body'  => 'Le produit a été ajouté avec succès.',
                                    ],

                                    'product-not-found' => [
                                        'title' => 'Produit introuvable',
                                    ],

                                    'product-already-exists' => [
                                        'title' => 'Le produit existe déjà',
                                        'body'  => 'Ce produit figure déjà dans les lignes de commande. Veuillez mettre à jour la ligne existante à la place.',
                                    ],

                                    'missing-product-data' => [
                                        'title' => 'Données produit manquantes',
                                        'body'  => 'Impossible de traiter le produit sélectionné.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Autres informations',

                'fieldset' => [
                    'sales' => [
                        'title' => 'Ventes',

                        'fields' => [
                            'sales-person'       => 'Vendeur',
                            'customer-reference' => 'Référence client',
                            'tags'               => 'Étiquettes',
                        ],
                    ],

                    'shipping' => [
                        'title'  => 'Livraison',
                        'fields' => [
                            'warehouse'       => 'Entrepôt',
                            'commitment-date' => 'Date de livraison',
                        ],
                    ],

                    'tracking' => [
                        'title'  => 'Suivi',
                        'fields' => [
                            'source-document' => 'Document source',
                            'medium'          => 'Support',
                            'source'          => 'Source',
                            'campaign'        => 'Campagne',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Informations complémentaires',

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

    'table' => [
        'columns' => [
            'number'             => 'Numéro',
            'status'             => 'Statut',
            'delivery-status'    => 'Statut de livraison',
            'invoice-status'     => 'Statut de facturation',
            'creation-date'      => 'Date de création',
            'commitment-date'    => 'Date d\'engagement',
            'expected-date'      => 'Date prévue',
            'customer'           => 'Client',
            'sales-person'       => 'Vendeur',
            'sales-team'         => 'Équipe commerciale',
            'untaxed-amount'     => 'Montant hors taxe',
            'amount-tax'         => 'Montant de la taxe',
            'amount-total'       => 'Montant total',
            'customer-reference' => 'Référence client',
        ],

        'summarizers' => [
            'total'        => 'Total',
            'taxes'        => 'Taxes',
            'total-amount' => 'Montant total',
        ],

        'filters' => [
            'sales-person'     => 'Vendeur',
            'utm-source'       => 'Source UTM',
            'company'          => 'Société',
            'customer'         => 'Client',
            'journal'          => 'Journal',
            'invoice-address'  => 'Adresse de facturation',
            'shipping-address' => 'Adresse de livraison',
            'fiscal-position'  => 'Position fiscale',
            'payment-term'     => 'Condition de paiement',
            'currency'         => 'Devise',
            'created-at'       => 'Créé le',
            'updated-at'       => 'Mis à jour le',
        ],

        'groups' => [
            'medium'          => 'Support',
            'source'          => 'Source',
            'team'            => 'Équipe',
            'sales-person'    => 'Vendeur',
            'currency'        => 'Devise',
            'company'         => 'Société',
            'customer'        => 'Client',
            'quotation-date'  => 'Date du devis',
            'commitment-date' => 'Date d\'engagement',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Devis restauré',
                    'body'  => 'Le devis a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Devis supprimé',
                    'body'  => 'Le devis a été supprimé avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Devis définitivement supprimé',
                    'body'  => 'Le devis a été définitivement supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Devis restaurés',
                    'body'  => 'Les devis ont été restaurés avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Devis supprimés',
                    'body'  => 'Les devis ont été supprimés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Devis définitivement supprimés',
                    'body'  => 'Les devis ont été définitivement supprimés avec succès.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Devis créés',
                    'body'  => 'Les devis ont été créés avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'Général',
                'entries' => [
                    'sale-order'     => 'Commande client',
                    'customer'       => 'Client',
                    'expiration'     => 'Expiration',
                    'quotation-date' => 'Date du devis',
                    'payment-term'   => 'Condition de paiement',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Ligne de commande',

                'repeater' => [
                    'products' => [
                        'title'       => 'Produits',
                        'add-product' => 'Ajouter un produit',
                        'entries'     => [
                            'product'             => 'Produit',
                            'product-variants'    => 'Variantes de produit',
                            'product-simple'      => 'Produit simple',
                            'quantity'            => 'Quantité',
                            'qty-delivered'       => 'Livrée',
                            'qty-invoiced'        => 'Facturée',
                            'uom'                 => 'UdM',
                            'lead-time'           => 'Délai',
                            'packaging-qty'       => 'Quantité de conditionnement',
                            'packaging'           => 'Conditionnement',
                            'unit-price'          => 'Prix unitaire',
                            'cost'                => 'Coût',
                            'margin'              => 'Marge',
                            'taxes'               => 'Taxes',
                            'amount'              => 'Montant',
                            'margin-percentage'   => 'Marge (%)',
                            'discount-percentage' => 'Remise (%)',
                            'sub-total'           => 'Sous-total',
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Produits optionnels',
                        'add-product' => 'Ajouter un produit',
                        'entries'     => [
                            'product'             => 'Produit',
                            'description'         => 'Description',
                            'quantity'            => 'Quantité',
                            'uom'                 => 'Unité de mesure',
                            'unit-price'          => 'Prix unitaire',
                            'discount-percentage' => 'Remise (%)',
                            'sub-total'           => 'Sous-total',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Ajouter une ligne de commande',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Produit ajouté',
                                        'body'  => 'Le produit a été ajouté avec succès.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Autres informations',

                'fieldset' => [
                    'sales' => [
                        'title' => 'Ventes',

                        'entries' => [
                            'sales-person'       => 'Vendeur',
                            'customer-reference' => 'Référence client',
                            'tags'               => 'Étiquettes',
                        ],
                    ],

                    'shipping' => [
                        'title'   => 'Livraison',
                        'entries' => [
                            'commitment-date' => 'Date de livraison',
                        ],
                    ],

                    'tracking' => [
                        'title'   => 'Suivi',
                        'entries' => [
                            'source-document' => 'Document source',
                            'medium'          => 'Support',
                            'source'          => 'Source',
                            'campaign'        => 'Campagne',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Informations complémentaires',

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
