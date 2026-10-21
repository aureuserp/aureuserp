<?php

return [
    'navigation' => [
        'title' => 'Modelos de observação',
        'group' => 'Ponto de venda',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Geral',

                'fields' => [
                    'name'  => 'Observação',
                    'color' => 'Cor',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'    => 'Observação',
            'color'   => 'Cor',
            'company' => 'Empresa',
        ],
    ],
];
