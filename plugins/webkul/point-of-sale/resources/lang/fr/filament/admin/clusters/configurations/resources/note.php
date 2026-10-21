<?php

return [
    'navigation' => [
        'title' => 'Modèles de note',
        'group' => 'Point de vente',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',

                'fields' => [
                    'name'  => 'Note',
                    'color' => 'Couleur',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'    => 'Note',
            'color'   => 'Couleur',
            'company' => 'Société',
        ],
    ],
];
