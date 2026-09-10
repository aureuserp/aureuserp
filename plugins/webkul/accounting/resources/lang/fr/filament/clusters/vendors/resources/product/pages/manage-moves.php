<?php

return [
    'title' => 'ENTRÉE/SORTIE',

    'table' => [
        'columns' => [
            'date'                 => 'Date',
            'reference'            => 'Référence',
            'product'              => 'Produit',
            'package'              => 'Colis',
            'lot'                  => 'Numéros de lot / série',
            'source-location'      => 'Emplacement source',
            'destination-location' => 'Emplacement de destination',
            'quantity'             => 'Quantité',
            'state'                => 'État',
            'done-by'              => 'Réalisé par',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Mouvement supprimé',
                    'body'  => 'Le mouvement a été supprimé avec succès.',
                ],
            ],
        ],
    ],
];
