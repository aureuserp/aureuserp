<?php

return [
    'navigation' => [
        'title' => 'Moedas/Cédulas',
        'group' => 'Ponto de venda',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Geral',

                'fields' => [
                    'name'                           => 'Nome',
                    'value'                          => 'Valor da moeda/cédula',
                    'is-for-all-configs'             => 'Para todos os PDVs',
                    'is-for-all-configs-helper-text' => 'Se marcado, esta moeda/cédula ficará disponível em todos os pontos de venda.',
                    'configs'                        => 'Pontos de venda',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Nome',
            'value'              => 'Valor da moeda/cédula',
            'is-for-all-configs' => 'Para todos os PDVs',
            'configs'            => 'Pontos de venda',
            'company'            => 'Empresa',
        ],
    ],
];
