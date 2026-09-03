<?php

return [
    'title' => 'Emplacements',

    'table' => [
        'columns' => [
            'product'          => 'Produit',
            'location'         => 'Emplacement',
            'storage-category' => 'Catégorie de stockage',
            'quantity'         => 'Quantité',
            'package'          => 'Colis',
            'on-hand'          => 'Quantité en stock',
            'unit'             => 'Unité',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Quantité supprimée',
                    'body'  => 'La quantité a été supprimée avec succès.',
                ],
            ],
        ],
    ],
];
