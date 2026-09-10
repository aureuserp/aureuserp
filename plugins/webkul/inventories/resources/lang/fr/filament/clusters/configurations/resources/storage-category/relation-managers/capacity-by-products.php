<?php

return [
    'title' => 'Capacité par produits',

    'form' => [
        'product' => 'Produit',
        'qty'     => 'Quantité',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Ajouter une capacité de produit',

                'notification' => [
                    'title' => 'Capacité de produit créée',
                    'body'  => 'La capacité de produit a été ajoutée avec succès.',
                ],
            ],
        ],

        'columns' => [
            'product' => 'Produit',
            'qty'     => 'Quantité',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Capacité de produit mise à jour',
                    'body'  => 'La capacité de produit a été mise à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Capacité de produit supprimée',
                    'body'  => 'La capacité de produit a été supprimée avec succès.',
                ],
            ],
        ],
    ],
];
