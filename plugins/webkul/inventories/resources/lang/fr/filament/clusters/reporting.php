<?php

return [
    'navigation' => [
        'title' => 'Rapports',
        'group' => 'Inventaire',
    ],

    'moves' => [
        'navigation' => [
            'title' => 'Historique des mouvements',
        ],

        'filters' => [
            'product-category'     => 'Catégorie de produit',
            'source-location'      => 'Emplacement source',
            'destination-location' => 'Emplacement de destination',
            'package'              => 'Colis',
            'lot'                  => 'Numéro de lot/série',
            'package-type'         => 'Type de colis',
        ],

        'groups' => [
            'product'   => 'Produit',
            'status'    => 'Statut',
            'date'      => 'Date',
            'operation' => 'Opération',
            'location'  => 'Emplacement',
            'category'  => 'Catégorie',
        ],
    ],

    'quantities' => [
        'navigation' => [
            'title' => 'Emplacements',
        ],

        'filters' => [
            'warehouse'        => 'Entrepôt',
            'location'         => 'Emplacement',
            'product-category' => 'Catégorie de produit',
            'storage-category' => 'Catégorie de stockage',
            'package'          => 'Colis',
            'lot'              => 'Numéro de lot/série',
            'package-type'     => 'Type de colis',
        ],

        'groups' => [
            'product'          => 'Produit',
            'product-category' => 'Catégorie de produit',
            'location'         => 'Emplacement',
            'storage-category' => 'Catégorie de stockage',
            'lot'              => 'Numéro de lot/série',
            'package'          => 'Colis',
            'company'          => 'Société',
        ],
    ],
];
