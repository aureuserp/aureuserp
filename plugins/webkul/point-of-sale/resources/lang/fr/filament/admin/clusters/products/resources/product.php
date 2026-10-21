<?php

return [
    'navigation' => [
        'title' => 'Produits',
    ],

    'tabs' => [
        'pos' => 'PDV',
    ],

    'form' => [
        'sections' => [
            'point-of-sale' => [
                'title'  => 'Point de vente',

                'fields' => [
                    'available-in-pos'        => 'Disponible en PdV',
                    'available-in-pos-helper' => 'Rendre ce produit disponible sur les terminaux du Point de vente.',
                    'categories'              => 'Catégories de produits PdV',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'point-of-sale' => [
                'title'   => 'Point de vente',

                'entries' => [
                    'available-in-pos' => 'Disponible en PdV',
                    'categories'       => 'Catégories de produits PdV',
                ],
            ],
        ],
    ],
];
