<?php

return [
    'navigation' => [
        'title' => 'Embalagens',
        'group' => 'Produtos',
    ],

    'form' => [
        'package-type' => 'Tipo de embalagem',
        'routes' => 'Rotas',
    ],

    'table' => [
        'columns' => [
            'package-type' => 'Tipo de embalagem',
        ],

        'groups' => [
            'package-type' => 'Tipo de embalagem',
        ],

        'filters' => [
            'package-type' => 'Tipo de embalagem',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'entries' => [
                    'package_type' => 'Tipo de embalagem',
                ],
            ],

            'routing' => [
                'title' => 'Informações de roteamento',

                'entries' => [
                    'routes' => 'Rotas do armazém',
                    'route_name' => 'Nome da rota',
                ],
            ],
        ],
    ],
];
