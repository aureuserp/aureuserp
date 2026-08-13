<?php

return [
    'title' => 'ENTRÉE/SORTIE',

    'table' => [
        'columns' => [
            'date'                 => 'Date',
            'reference'            => 'Référence',
            'product'              => 'Produit',
            'package'              => 'Colis',
            'lot'                  => 'Lot / Numéros de série',
            'source-location'      => 'Emplacement source',
            'destination-location' => 'Emplacement de destination',
            'quantity'             => 'Quantité',
            'state'                => 'État',
            'done-by'              => 'Effectué par',
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
