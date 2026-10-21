<?php

return [
    'navigation' => [
        'title' => 'Andares',
        'group' => 'Ponto de venda',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'name'                => 'Nome',
                    'company'             => 'Empresa',
                    'background-color'    => 'Cor de fundo',
                    'background-image'    => 'Imagem de fundo',
                    'configs'             => 'Ponto de venda',
                    'configs-helper-text' => 'Terminais no modo restaurante que usam este andar.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'entries' => [
                    'name'             => 'Nome',
                    'background-color' => 'Cor de fundo',
                    'company-name'     => 'Empresa',
                    'configs'          => 'Ponto de venda',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'Nome',
            'tables'           => 'Mesas',
            'configs'          => 'Ponto de venda',
            'background-color' => 'Cor de fundo',
            'company'          => 'Empresa',
        ],
    ],
];
