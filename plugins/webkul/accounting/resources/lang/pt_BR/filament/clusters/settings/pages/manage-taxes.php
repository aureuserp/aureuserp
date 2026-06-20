<?php

return [
    'title' => 'Gerenciar impostos',

    'form' => [
        'default-taxes' => [
            'label' => 'Impostos padrão',
            'helper-text' => 'O padrão será aplicado aos produtos se nenhum imposto for selecionado',
        ],

        'sales-tax' => [
            'label' => 'Imposto sobre vendas',
        ],

        'purchase-tax' => [
            'label' => 'Imposto sobre compras',
        ],

        'prices' => [
            'label' => 'Preços',
        ],

        'rounding-method' => [
            'label' => 'Método de arredondamento',
            'helper-text' => 'Método usado para arredondar valores de impostos',

            'options' => [
                'round-per-line' => 'Arredondar por linha',
                'round-globally' => 'Arredondar globalmente',
            ],
        ],

        'fiscal-country' => [
            'label' => 'País fiscal',
        ],
    ],
];
