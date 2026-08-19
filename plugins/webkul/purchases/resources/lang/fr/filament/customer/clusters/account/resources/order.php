<?php

return [
    'table' => [
        'columns' => [
            'reference'         => 'Référence',
            'total-amount'      => 'Montant total',
            'confirmation-date' => 'Date de confirmation',
            'status'            => 'Statut',
        ],
    ],

    'products' => [
        'columns' => [
            'product'    => 'Produit',
            'quantity'   => 'Quantité',
            'unit-price' => 'Prix unitaire',
            'taxes'      => 'Taxes',
            'discount'   => 'Remise %',
            'amount'     => 'Montant',
        ],
    ],

    'infolist' => [
        'settings' => [
            'entries' => [
                'buyer' => 'Acheteur',
            ],

            'actions' => [
                'accept' => [
                    'label' => 'Accepter',

                    'notification' => [
                        'title' => 'Devis accepté',
                        'body'  => 'La demande de prix a été acceptée avec succès.',
                    ],

                    'message' => [
                        'body' => 'La demande de prix a été acceptée par le fournisseur.',
                    ],
                ],

                'decline' => [
                    'label' => 'Refuser',

                    'notification' => [
                        'title' => 'Devis refusé',
                        'body'  => 'La demande de prix a été refusée avec succès.',
                    ],

                    'message' => [
                        'body' => 'La demande de prix a été refusée par le fournisseur.',
                    ],
                ],

                'print' => [
                    'label' => 'Télécharger/Imprimer',
                ],
            ],
        ],

        'general' => [
            'entries' => [
                'purchase-order'        => 'Bon de commande #:id',
                'quotation'             => 'Demande de prix #:id',
                'order-date'            => 'Date de commande',
                'from'                  => 'De',
                'confirmation-date'     => 'Date de confirmation',
                'receipt-date'          => 'Date de réception',
                'products'              => 'Produits',
                'untaxed-amount'        => 'Montant hors taxes',
                'tax-amount'            => 'Montant de la taxe',
                'total'                 => 'Total',
                'communication-history' => 'Historique des communications',
            ],
        ],
    ],
];
