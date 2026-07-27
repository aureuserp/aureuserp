<?php

return [
    'navigation' => [
        'title' => 'Categorias',
        'group' => 'Produtos',
    ],

    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'Estoque',

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Logística',

                        'fields' => [
                            'routes' => 'Rotas',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'inventory' => [
                'title' => 'Estoque',

                'subsections' => [
                    'logistics' => [
                        'title' => 'Logística',

                        'entries' => [
                            'routes' => 'Rotas do armazém',
                            'route_name' => 'Nome da rota',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
