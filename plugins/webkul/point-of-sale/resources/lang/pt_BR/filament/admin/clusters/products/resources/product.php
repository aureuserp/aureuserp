<?php

return [
    'navigation' => [
        'title' => 'Produtos',
    ],

    'tabs' => [
        'pos' => 'PDV',
    ],

    'form' => [
        'sections' => [
            'point-of-sale' => [
                'title'  => 'Ponto de venda',

                'fields' => [
                    'available-in-pos'        => 'Disponível no PDV',
                    'available-in-pos-helper' => 'Torne este produto disponível nos terminais do Ponto de venda.',
                    'categories'              => 'Categorias de produtos do PDV',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'point-of-sale' => [
                'title'   => 'Ponto de venda',

                'entries' => [
                    'available-in-pos' => 'Disponível no PDV',
                    'categories'       => 'Categorias de produtos do PDV',
                ],
            ],
        ],
    ],
];
