<?php

return [
    'navigation' => [
        'title' => 'Quantités',
        'group' => 'Ajustements',
    ],

    'form' => [
        'fields' => [
            'location'         => 'Emplacement',
            'product'          => 'Produit',
            'package'          => 'Colis',
            'lot'              => 'Numéros de lot / série',
            'counted-qty'      => 'Quantité comptée',
            'scheduled-at'     => 'Planifié le',
            'storage-category' => 'Catégorie de stockage',
        ],
    ],

    'table' => [
        'columns' => [
            'location'           => 'Emplacement',
            'product'            => 'Produit',
            'product-category'   => 'Catégorie de produit',
            'lot'                => 'Numéros de lot / série',
            'storage-category'   => 'Catégorie de stockage',
            'available-quantity' => 'Quantité disponible',
            'quantity'           => 'Quantité',
            'package'            => 'Colis',
            'last-counted-at'    => 'Dernier comptage le',
            'on-hand'            => 'Quantité en stock',
            'uom'                => 'UdM',
            'counted'            => 'Quantité comptée',
            'difference'         => 'Différence',
            'scheduled-at'       => 'Planifié le',
            'user'               => 'Utilisateur',
            'company'            => 'Société',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Quantité mise à jour',
                    'body'  => 'La quantité a été mise à jour avec succès.',
                ],
            ],
        ],

        'groups' => [
            'product'          => 'Produit',
            'product-category' => 'Catégorie de produit',
            'location'         => 'Emplacement',
            'storage-category' => 'Catégorie de stockage',
            'lot'              => 'Numéros de lot / série',
            'company'          => 'Société',
            'package'          => 'Colis',
        ],

        'filters' => [
            'product'             => 'Produit',
            'uom'                 => 'Unité de mesure',
            'product-category'    => 'Catégorie de produit',
            'location'            => 'Emplacement',
            'storage-category'    => 'Catégorie de stockage',
            'lot'                 => 'Numéros de lot / série',
            'company'             => 'Société',
            'package'             => 'Colis',
            'on-hand-quantity'    => 'Quantité en stock',
            'difference-quantity' => 'Quantité de différence',
            'incoming-at'         => 'Arrivée le',
            'scheduled-at'        => 'Planifié le',
            'user'                => 'Utilisateur',
            'created-at'          => 'Créé le',
            'updated-at'          => 'Mis à jour le',
            'company'             => 'Société',
            'creator'             => 'Créateur',
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
                        'body'  => 'Une quantité existe déjà pour cette configuration. Veuillez mettre à jour la quantité existante à la place.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'apply' => [
                'label' => 'Appliquer',

                'notification' => [
                    'title' => 'Modifications de quantité appliquées',
                    'body'  => 'Les modifications de quantité ont été appliquées avec succès.',
                ],
            ],

            'clear' => [
                'label' => 'Effacer',

                'notification' => [
                    'title' => 'Modifications de quantité effacées',
                    'body'  => 'Les modifications de quantité ont été effacées avec succès.',
                ],
            ],
        ],
    ],
];
