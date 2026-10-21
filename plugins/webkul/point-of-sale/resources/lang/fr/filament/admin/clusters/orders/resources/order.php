<?php

return [

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Général',
                'fields' => [
                    'order'      => 'Commande du point de vente',
                    'ordered-at' => 'Date',
                    'customer'   => 'Client',
                    'cashier'    => 'Caissier',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title'   => 'Produits',
                'columns' => [
                    'product'            => 'Produit',
                    'lot'                => 'Numéro de lot/série',
                    'quantity'           => 'Quantité',
                    'uom'                => 'UdM',
                    'unit-price'         => 'Prix unitaire',
                    'discount'           => 'Rem. %',
                    'taxes'              => 'Taxes',
                    'tax-excluded'       => 'HT',
                    'tax-included'       => 'TTC',
                    'full-product-name'  => 'Nom complet du produit',
                    'customer-note'      => 'Note du client',
                    'total-cost'         => 'Coût total',
                    'margin'             => 'Marge',
                    'margin-percent'     => 'Marge (%)',
                    'refunded-quantity'  => 'Quantité remboursée',
                ],

                'actions' => [
                    'open-product' => 'Ouvrir le produit',
                ],

                'summary' => [
                    'untaxed'  => 'Montant hors taxes',
                    'taxes'    => 'Taxes',
                    'rounding' => 'Arrondi',
                    'total'    => 'Total',
                    'paid'     => 'Payé',
                    'change'   => 'Monnaie',
                ],
            ],

            'payments' => [
                'title'  => 'Paiements',
                'add'    => 'Ajouter une ligne',
                'fields' => [
                    'paid-at'         => 'Date',
                    'method'          => 'Mode de paiement',
                    'amount'          => 'Montant',
                    'card-type'       => 'Numéro de carte (4 derniers chiffres)',
                    'card-brand'      => 'Marque de la carte',
                    'cardholder-name' => 'Nom du titulaire',
                ],
            ],

            'extra-info' => [
                'title'  => 'Informations supplémentaires',
                'fields' => [
                    'receipt-number'  => 'Numéro de reçu',
                    'tracking-number' => 'Numéro de suivi',
                    'email'           => 'E-mail',
                    'mobile'          => 'Mobile',
                ],
            ],

            'notes' => [
                'title' => 'Notes générales',
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'Général',
                'entries' => [
                    'order'         => 'Commande du point de vente',
                    'customer'      => 'Client',
                    'session'       => 'Session',
                    'ordered-at'    => 'Commandé le',
                    'point-of-sale' => 'Point de vente',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title'   => 'Ligne de commande',
                'entries' => [
                    'product'    => 'Produit',
                    'quantity'   => 'Quantité',
                    'unit-price' => 'Prix unitaire',
                    'taxes'      => 'Taxes',
                    'discount'   => 'Remise (%)',
                    'amount'     => 'Montant',
                ],
                'totals' => [
                    'untaxed' => 'Montant hors taxes',
                    'taxes'   => 'Taxes',
                    'total'   => 'Montant total',
                    'margin'  => 'Marge',
                ],
            ],

            'payments' => [
                'title'   => 'Paiements',
                'entries' => [
                    'method'  => 'Mode de paiement',
                    'amount'  => 'Montant',
                    'paid-at' => 'Payé le',
                ],
            ],

            'extra-info' => [
                'title'   => 'Informations supplémentaires',
                'entries' => [
                    'receipt-number'  => 'Numéro de reçu',
                    'tracking-number' => 'Numéro de suivi',
                    'email'           => 'E-mail',
                    'mobile'          => 'Mobile',
                ],
            ],

            'other-information' => [
                'title'   => 'Autres informations',
                'entries' => [
                    'reference'      => 'Référence',
                    'receipt-number' => 'Numéro de reçu',
                    'operation'      => 'Opération',
                    'cashier'        => 'Caissier',
                    'paid'           => 'Payé',
                    'change'         => 'Monnaie',
                ],
            ],
        ],
    ],
    'navigation' => [
        'title' => 'Commandes',
        'group' => 'Point de vente',
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Réf. commande',
            'reference'            => 'Numéro de reçu',
            'session'              => 'Session',
            'config'               => 'Point de vente',
            'partner'              => 'Client',
            'ordered-at'           => 'Date',
            'amount-total'         => 'Total',
            'amount-paid'          => 'Payé',
            'user'                 => 'Caissier',
            'has-failed-operation' => 'Opération échouée',
            'is-invoiced'          => 'Facturé',
            'is-edited'            => 'Modifiée',
            'tracking-number'      => 'Numéro de suivi',
            'state'                => 'Statut',
        ],

        'groups' => [
            'session'    => 'Session',
            'config'     => 'Point de vente',
            'state'      => 'Statut',
            'ordered-at' => 'Commandé le',
        ],

        'filters' => [
            'state'   => 'Statut',
            'session' => 'Session',
            'config'  => 'Point de vente',
        ],
    ],
];
