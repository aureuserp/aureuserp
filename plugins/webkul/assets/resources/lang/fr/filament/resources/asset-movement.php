<?php

return [
    'navigation' => [
        'title' => 'Mouvements',
    ],

    'label'        => "Mouvement d'actif",
    'plural-label' => "Mouvements d'actifs",

    'table' => [
        'columns' => [
            'occurred-at'        => 'Date et heure',
            'asset-tag'          => "Étiquette d'actif",
            'asset-name'         => "Nom de l'actif",
            'type'               => 'Type de mouvement',
            'quantity'           => 'Quantité',
            'source-type'        => 'Origine',
            'from'               => 'De',
            'to'                 => 'À',
            'condition'          => 'État',
            'performed-by'       => 'Effectué par',
            'expected-return-at' => 'Retour prévu',
            'note'               => 'Note',
            'attachments'        => 'Pièces jointes',
        ],
        'groups' => [
            'date'         => 'Date',
            'type'         => 'Type de mouvement',
            'asset'        => 'Actif',
            'condition'    => 'État',
            'performed-by' => 'Effectué par',
        ],
        'filters' => [
            'type'         => 'Type de mouvement',
            'product-type' => 'Type de Produit',
            'source-type'  => 'Origine',
            'condition'    => 'État',
            'category'     => 'Catégorie',
            'product'      => 'Produit',
            'asset'        => 'Actif',
            'warehouse'    => 'Entrepôt',
            'employee'     => 'Employé',
            'department'   => 'Département',
            'site'         => 'Site',
            'performed-by' => 'Effectué par',
            'date-from'    => 'Date de début',
            'date-until'   => 'Date de fin',
        ],
        'actions' => [
            'view' => 'Voir les détails',
        ],
        'toolbar-actions' => [
            'export' => [
                'label' => 'Exporter',
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'tabs' => [
                'all'           => 'Tous',
                'assets'        => 'Actifs',
                'consumables'   => 'Consommables',
                'issues'        => 'Sorties',
                'returns'       => 'Retours',
                'transfers'     => 'Transferts',
                'scrapped-lost' => 'Mis au rebut / Perdu',
                'damaged'       => 'Endommagé / Mauvais',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'details'     => 'Informations du mouvement',
            'attachments' => 'Pièces jointes et documentation',
        ],
    ],
];
