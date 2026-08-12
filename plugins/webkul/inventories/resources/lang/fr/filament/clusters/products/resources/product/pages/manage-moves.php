<?php

return [
    'title' => 'Entrées/Sorties',

    'tabs' => [
        'todo'     => 'À faire',
        'done'     => 'Terminé',
        'incoming' => 'Entrant',
        'outgoing' => 'Sortant',
        'internal' => 'Interne',
    ],

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
            'unit'                 => 'Unité',
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
