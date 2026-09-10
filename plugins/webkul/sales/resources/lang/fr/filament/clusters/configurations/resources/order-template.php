<?php

return [
    'title' => 'Produits du modèle de commande',

    'navigation' => [
        'title' => 'Produits du modèle de commande',
        'group' => 'Commandes clients',
    ],

    'global-search' => [
        'name'    => 'Nom',
    ],

    'form' => [
        'fields' => [
            'sort'           => 'Tri',
            'order-template' => 'Modèle de commande',
            'company'        => 'Société',
            'product'        => 'Produit',
            'product-uom'    => 'UdM du produit',
            'creator'        => 'Créateur',
            'display-type'   => 'Type d\'affichage',
            'name'           => 'Nom',
            'quantity'       => 'Quantité',
        ],
    ],

    'table' => [
        'columns' => [
            'sort'           => 'Tri',
            'order-template' => 'Modèle de commande',
            'company'        => 'Société',
            'product'        => 'Produit',
            'product-uom'    => 'UdM du produit',
            'created-by'     => 'Créé par',
            'display-type'   => 'Type d\'affichage',
            'name'           => 'Nom',
            'quantity'       => 'Quantité',
            'created-at'     => 'Créé le',
            'updated-at'     => 'Mis à jour le',

        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Produits du modèle de commande mis à jour',
                    'body'  => 'Les produits du modèle de commande ont été mis à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Produits du modèle de commande supprimés',
                    'body'  => 'Les produits du modèle de commande ont été supprimés avec succès.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Produits du modèle de commande supprimés',
                    'body'  => 'Les produits du modèle de commande ont été supprimés avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'sort'           => 'Ordre de tri',
            'order-template' => 'Modèle de commande',
            'company'        => 'Société',
            'product'        => 'Produit',
            'product-uom'    => 'UdM du produit',
            'display-type'   => 'Type d\'affichage',
            'name'           => 'Nom',
            'quantity'       => 'Quantité',
        ],
    ],
];
