<?php

return [
    'label' => 'Etiquetas',

    'form' => [
        'fields' => [
            'type' => 'Tipo de etiquetas',
            'quantity' => 'Quantidade',
            'format' => 'Formato',
            'layout' => 'Layout das etiquetas',
            'quantity-type' => 'Quantidade para imprimir',
            'quantity' => 'Quantidade',

            'quantity-type-options' => [
                'operation' => 'Quantidade da operação',
                'custom' => 'Quantidade personalizada',
                'per-slot' => 'Uma por lote/NS',
                'per-unit' => 'Uma por unidade',
            ],

            'type-options' => [
                'product' => 'Etiquetas de produto',
                'lot' => 'Etiquetas de lote/NS',
            ],

            'format-options' => [
                'dymo' => 'Dymo',
                '2x7_price' => '2x7 com preço',
                '4x7_price' => '4x7 com preço',
                '4x12' => '4x12',
                '4x12_price' => '4x12 com preço',
            ],
        ],
    ],
];
