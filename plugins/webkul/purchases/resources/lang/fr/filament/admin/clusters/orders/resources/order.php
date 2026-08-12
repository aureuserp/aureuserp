<?php

return [
    'global-search' => [
        'vendor'    => 'Fournisseur',
        'reference' => 'Référence',
        'amount'    => 'Montant',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'fields' => [
                    'vendor'                   => 'Fournisseur',
                    'vendor-reference'         => 'Référence fournisseur',
                    'vendor-reference-tooltip' => 'Le numéro de référence de la commande de vente ou de l\'offre fourni par le fournisseur. Il est utilisé pour la correspondance lors de la réception des produits, car cette référence est généralement incluse dans le bon de livraison du fournisseur.',
                    'agreement'                => 'Accord',
                    'currency'                 => 'Devise',
                    'confirmation-date'        => 'Date de confirmation',
                    'order-deadline'           => 'Date limite de commande',
                    'expected-arrival'         => 'Arrivée prévue',
                    'confirmed-by-vendor'      => 'Confirmé par le fournisseur',
                    'deliver-to'               => 'Livrer à',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Produits',

                'repeater' => [
                    'products' => [
                        'title'            => 'Produits',
                        'add-product-line' => 'Ajouter un produit',

                        'fields' => [
                            'product'             => 'Produit',
                            'expected-arrival'    => 'Arrivée prévue',
                            'quantity'            => 'Quantité',
                            'received'            => 'Reçu',
                            'billed'              => 'Facturé',
                            'unit'                => 'Unité',
                            'packaging-qty'       => 'Qté d\'emballage',
                            'packaging'           => 'Emballage',
                            'taxes'               => 'Taxes',
                            'discount-percentage' => 'Remise (%)',
                            'unit-price'          => 'Prix unitaire',
                            'amount'              => 'Montant',
                        ],

                        'notifications' => [
                            'quantity-below-received' => [
                                'title' => 'Impossible de réduire la quantité',
                                'body'  => 'Vous ne pouvez pas réduire la quantité en dessous de la quantité reçue (:qty).',
                            ],

                            'blanket-order-qty-limit' => [
                                'title' => 'La quantité dépasse la limite de la commande ouverte',
                                'body'  => 'La quantité du produit (:product_qty) dépasse la quantité disponible (:available_qty) de la commande ouverte.',
                            ],
                        ],

                        'columns' => [
                            'product'             => 'Produit',
                            'expected-arrival'    => 'Arrivée prévue',
                            'quantity'            => 'Quantité',
                            'received'            => 'Reçu',
                            'billed'              => 'Facturé',
                            'unit'                => 'Unité',
                            'packaging-qty'       => 'Qté d\'emballage',
                            'packaging'           => 'Emballage',
                            'taxes'               => 'Taxes',
                            'discount-percentage' => 'Remise (%)',
                            'unit-price'          => 'Prix unitaire',
                            'amount'              => 'Montant',
                        ],

                        'delete-action' => [
                            'error' => [
                                'title' => 'Impossible de supprimer le produit',
                                'body'  => 'Les produits ne peuvent pas être supprimés d\'un bon de commande confirmé.',
                            ],
                        ],

                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Ouvrir le produit',
                            ],
                        ],
                    ],

                    'section' => [
                        'title' => 'Ajouter une section',

                        'fields' => [],
                    ],

                    'note' => [
                        'title' => 'Ajouter une note',

                        'fields' => [],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Informations complémentaires',

                'fields' => [
                    'buyer'             => 'Acheteur',
                    'company'           => 'Société',
                    'source-document'   => 'Document source',
                    'incoterm'          => 'Incoterm',
                    'incoterm-tooltip'  => 'Les Incoterms (International Commercial Terms) sont un ensemble de termes commerciaux normalisés utilisés dans les transactions internationales pour définir les responsabilités entre acheteurs et vendeurs.',
                    'incoterm-location' => 'Lieu de l\'Incoterm',
                    'payment-term'      => 'Condition de paiement',
                    'fiscal-position'   => 'Position fiscale',
                ],
            ],

            'terms' => [
                'title' => 'Termes et conditions',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'         => 'Favori',
            'priority'         => 'Priorité',
            'vendor-reference' => 'Référence fournisseur',
            'reference'        => 'Référence',
            'vendor'           => 'Fournisseur',
            'buyer'            => 'Acheteur',
            'company'          => 'Société',
            'order-deadline'   => 'Date limite de commande',
            'source-document'  => 'Document source',
            'untaxed-amount'   => 'Montant hors taxes',
            'total-amount'     => 'Montant total',
            'status'           => 'Statut',
            'billing-status'   => 'Statut de facturation',
            'receipt-status'   => 'Statut de réception',
            'currency'         => 'Devise',
        ],

        'groups' => [
            'vendor'     => 'Fournisseur',
            'buyer'      => 'Acheteur',
            'state'      => 'État',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'filters' => [
            'status'           => 'Statut',
            'vendor-reference' => 'Référence fournisseur',
            'reference'        => 'Référence',
            'untaxed-amount'   => 'Montant hors taxes',
            'total-amount'     => 'Montant total',
            'order-deadline'   => 'Date limite de commande',
            'vendor'           => 'Fournisseur',
            'buyer'            => 'Acheteur',
            'company'          => 'Société',
            'payment-term'     => 'Condition de paiement',
            'incoterm'         => 'Incoterm',
            'status'           => 'Statut',
            'created-at'       => 'Créé le',
            'updated-at'       => 'Mis à jour le',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Commande supprimée',
                        'body'  => 'La commande a été supprimée avec succès.',
                    ],

                    'error' => [
                        'title' => 'La commande n\'a pas pu être supprimée',
                        'body'  => 'La commande ne peut pas être supprimée car elle est actuellement utilisée.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Commandes supprimées',
                        'body'  => 'Les commandes ont été supprimées avec succès.',
                    ],

                    'error' => [
                        'title' => 'Les commandes n\'ont pas pu être supprimées',
                        'body'  => 'Les commandes ne peuvent pas être supprimées car elles sont actuellement utilisées.',
                    ],
                ],
            ],
        ],
    ],

    'summary' => [
        'tax' => 'Taxe',
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'entries' => [
                    'purchase-order'           => 'Bon de commande',
                    'vendor'                   => 'Fournisseur',
                    'vendor-reference'         => 'Référence fournisseur',
                    'vendor-reference-tooltip' => 'Le numéro de référence de la commande de vente ou de l\'offre fourni par le fournisseur. Il est utilisé pour la correspondance lors de la réception des produits, car cette référence est généralement incluse dans le bon de livraison du fournisseur.',
                    'agreement'                => 'Accord',
                    'currency'                 => 'Devise',
                    'confirmation-date'        => 'Date de confirmation',
                    'order-deadline'           => 'Date limite de commande',
                    'expected-arrival'         => 'Arrivée prévue',
                    'confirmed-by-vendor'      => 'Confirmé par le fournisseur',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Produits',

                'repeater' => [
                    'products' => [
                        'title'            => 'Produits',
                        'add-product-line' => 'Ajouter un produit',

                        'entries' => [
                            'product'             => 'Produit',
                            'expected-arrival'    => 'Arrivée prévue',
                            'quantity'            => 'Quantité',
                            'received'            => 'Reçu',
                            'billed'              => 'Facturé',
                            'unit'                => 'Unité',
                            'packaging-qty'       => 'Qté d\'emballage',
                            'packaging'           => 'Emballage',
                            'taxes'               => 'Taxes',
                            'discount-percentage' => 'Remise (%)',
                            'unit-price'          => 'Prix unitaire',
                            'amount'              => 'Montant',
                        ],
                    ],

                    'section' => [
                        'title' => 'Ajouter une section',
                    ],

                    'note' => [
                        'title' => 'Ajouter une note',
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Informations complémentaires',

                'entries' => [
                    'buyer'             => 'Acheteur',
                    'company'           => 'Société',
                    'source-document'   => 'Document source',
                    'incoterm'          => 'Incoterm',
                    'incoterm-tooltip'  => 'Les Incoterms (International Commercial Terms) sont un ensemble de termes commerciaux normalisés utilisés dans les transactions internationales pour définir les responsabilités entre acheteurs et vendeurs.',
                    'incoterm-location' => 'Lieu de l\'Incoterm',
                    'payment-term'      => 'Condition de paiement',
                    'fiscal-position'   => 'Position fiscale',
                ],
            ],

            'terms' => [
                'title' => 'Termes et conditions',
            ],
        ],
    ],
];
