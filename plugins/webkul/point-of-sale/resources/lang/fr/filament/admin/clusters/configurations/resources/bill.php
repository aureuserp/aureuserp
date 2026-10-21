<?php

return [
    'navigation' => [
        'title' => 'Pièces/Billets',
        'group' => 'Point de vente',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',

                'fields' => [
                    'name'                           => 'Nom',
                    'value'                          => 'Valeur de la pièce/du billet',
                    'is-for-all-configs'             => 'Pour tous les PdV',
                    'is-for-all-configs-helper-text' => 'Si coché, cette pièce/ce billet sera disponible dans tous les points de vente.',
                    'configs'                        => 'Points de vente',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Nom',
            'value'              => 'Valeur de la pièce/du billet',
            'is-for-all-configs' => 'Pour tous les PdV',
            'configs'            => 'Points de vente',
            'company'            => 'Société',
        ],
    ],
];
