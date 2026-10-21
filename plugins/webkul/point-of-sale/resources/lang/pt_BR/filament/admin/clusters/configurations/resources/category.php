<?php

return [
    'navigation' => [
        'title' => 'Categorias de produtos do PDV',
        'group' => 'Ponto de venda',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Geral',

                'fields' => [
                    'name'    => 'Nome',
                    'parent'  => 'Categoria pai',
                    'company' => 'Empresa',
                    'color'   => 'Cor',
                    'image'   => 'Imagem',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Geral',

                'entries' => [
                    'name'         => 'Nome',
                    'parent-name'  => 'Categoria pai',
                    'color'        => 'Cor',
                    'company-name' => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'    => 'Nome',
            'parent'  => 'Categoria pai',
            'color'   => 'Cor',
            'company' => 'Empresa',
        ],

        'groups' => [
            'parent' => 'Categoria pai',
        ],
    ],
];
