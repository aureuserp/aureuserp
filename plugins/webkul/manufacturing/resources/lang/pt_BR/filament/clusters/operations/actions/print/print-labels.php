<?php

return [
    'label' => 'Imprimir etiquetas',

    'form' => [
        'fields' => [
            'quantity' => 'Quantidade',
            'format' => 'Formato',
            'quantity-type' => 'Quantidade para imprimir',

            'quantity-type-options' => [
                'operation' => 'Quantidade da operação',
                'custom' => 'Quantidade personalizada',
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
