<?php

return [
    'navigation' => [
        'title' => 'Étages',
        'group' => 'Point de vente',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'fields' => [
                    'name'                => 'Nom',
                    'company'             => 'Société',
                    'background-color'    => 'Couleur de fond',
                    'background-image'    => 'Image de fond',
                    'configs'             => 'Point de vente',
                    'configs-helper-text' => 'Terminaux en mode restaurant qui utilisent cet étage.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'entries' => [
                    'name'             => 'Nom',
                    'background-color' => 'Couleur de fond',
                    'company-name'     => 'Société',
                    'configs'          => 'Point de vente',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'Nom',
            'tables'           => 'Tables',
            'configs'          => 'Point de vente',
            'background-color' => 'Couleur de fond',
            'company'          => 'Société',
        ],
    ],
];
