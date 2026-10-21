<?php

return [
    'navigation' => [
        'title' => 'Impressoras de preparo',
        'group' => 'Ponto de venda',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Geral',

                'fields' => [
                    'name'         => 'Nome',
                    'printer-type' => 'Tipo de impressora',
                    'proxy-ip'     => 'IP do proxy',
                    'company'      => 'Empresa',
                    'categories'   => 'Categorias impressas',
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
                    'printer-type' => 'Tipo de impressora',
                    'proxy-ip'     => 'Endereço IP',
                    'company-name' => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nome',
            'printer-type' => 'Tipo de impressora',
            'proxy-ip'     => 'IP do proxy',
            'categories'   => 'Categorias',
            'company'      => 'Empresa',
        ],

        'filters' => [
            'printer-type' => 'Tipo de impressora',
        ],
    ],
];
