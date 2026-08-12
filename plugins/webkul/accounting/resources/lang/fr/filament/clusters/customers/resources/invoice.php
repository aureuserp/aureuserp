<?php

return [
    'title' => 'Facture',

    'navigation' => [
        'title' => 'Factures',
    ],

    'global-search' => [
        'customer' => 'Client',
        'date'     => 'Date',
        'due-date' => 'Date d\'échéance',
        'amount'   => 'Montant',
    ],

    'form' => [
        'tabs' => [
            'invoice-lines' => [
                'repeater' => [
                    'products' => [
                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Ouvrir le produit',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
