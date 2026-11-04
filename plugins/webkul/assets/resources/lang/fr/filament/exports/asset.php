<?php

return [
    'columns' => [
        'asset-tag'          => 'Étiquette de l\'actif',
        'product'            => 'Produit',
        'product-type'       => 'Type de Produit',
        'initial-quantity'   => 'Quantité initiale',
        'quantity'           => 'Quantité',
        'model'              => 'Modèle',
        'category'           => 'Catégorie',
        'serial-no'          => 'Numéro de série',
        'status'             => 'Statut',
        'condition'          => 'État',
        'warehouse'          => 'Entrepôt',
        'current-employee'   => 'Employé actuel',
        'department'         => 'Département',
        'current-site'       => 'Site actuel',
        'site-type'          => 'Type de site',
        'issued-at'          => 'Délivré le',
        'expected-return-at' => 'Date de retour prévue',
        'cost'               => 'Coût',
        'purchase-date'      => 'Date d\'achat',
        'warranty-until'     => 'Garantie jusqu\'au',
        'custody-note'       => 'Note de garde',
        'note'               => 'Note',
        'company'            => 'Société',
        'created-at'         => 'Créé le',
    ],

    'options' => [
        'section'    => 'Filtrer les actifs exportés (Optionnel)',
        'products'   => 'Produits',
        'statuses'   => 'Statuts',
        'conditions' => 'États',
        'categories' => 'Catégories',
        'warehouses' => 'Entrepôts',
        'sites'      => 'Sites',
        'employees'  => 'Employés',
    ],

    'notification' => [
        'completed' => 'Votre export d\'actifs est terminé et :count ligne(s) exportée(s).',
        'failed'    => ':count ligne(s) n\'ont pas pu être exportée(s).',
    ],
];
