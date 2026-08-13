<?php

return [
    'title' => 'Quantités',

    'tabs' => [
        'internal-locations' => 'Emplacements internes',
        'transit-locations'  => 'Emplacements de transit',
        'on-hand'            => 'En stock',
        'to-count'           => 'À compter',
        'to-apply'           => 'À appliquer',
    ],

    'form' => [
        'fields' => [
            'product'          => 'Produit',
            'location'         => 'Emplacement',
            'package'          => 'Colis',
            'lot'              => 'Lot / Numéros de série',
            'on-hand-qty'      => 'Quantité en stock',
            'storage-category' => 'Catégorie de stockage',
        ],
    ],

    'table' => [
        'columns' => [
            'product'           => 'Produit',
            'location'          => 'Emplacement',
            'lot'               => 'Lot / Numéros de série',
            'storage-category'  => 'Catégorie de stockage',
            'quantity'          => 'Quantité',
            'package'           => 'Colis',
            'on-hand'           => 'Quantité en stock',
            'unit'              => 'Unité',
            'reserved-quantity' => 'Quantité réservée',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Quantité mise à jour',
                    'body'  => 'La quantité a été mise à jour avec succès.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Ajouter une quantité',

                'notification' => [
                    'title' => 'Quantité ajoutée',
                    'body'  => 'La quantité a été ajoutée avec succès.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'La quantité existe déjà',
                        'body'  => 'Une quantité existe déjà pour la même configuration. Veuillez plutôt mettre à jour la quantité.',
                    ],
                ],
            ],
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
