<?php

return [
    'navigation' => [
        'title' => 'Réapprovisionnement',
        'group' => 'Approvisionnement',
    ],

    'form' => [
        'fields' => [
        ],
    ],

    'table' => [
        'columns' => [
            'product'           => 'Produit',
            'location'          => 'Emplacement',
            'route'             => 'Route',
            'vendor'            => 'Fournisseur',
            'trigger'           => 'Déclencheur',
            'on-hand'           => 'En stock',
            'min'               => 'Min',
            'max'               => 'Max',
            'multiple-quantity' => 'Quantité multiple',
            'to-order'          => 'À commander',
            'uom'               => 'UdM',
            'company'           => 'Société',
        ],

        'groups' => [
            'location' => 'Emplacement',
            'product'  => 'Produit',
            'category' => 'Catégorie',
        ],

        'filters' => [
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Ajouter un réapprovisionnement',

                'notification' => [
                    'title' => 'Réapprovisionnement ajouté',
                    'body'  => 'Le réapprovisionnement a été ajouté avec succès.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'Le réapprovisionnement existe déjà',
                        'body'  => 'Un réapprovisionnement existe déjà pour cette configuration. Veuillez mettre à jour le réapprovisionnement existant à la place.',
                    ],
                ],
            ],
        ],

        'actions' => [
        ],
    ],
];
