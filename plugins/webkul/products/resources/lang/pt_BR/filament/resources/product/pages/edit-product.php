<?php

return [
    'notification' => [
        'title' => 'Produto atualizado',
        'body' => 'O produto foi atualizado com sucesso.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Imprimir etiquetas',

            'form' => [
                'fields' => [
                    'quantity' => 'Número de etiquetas',
                    'format' => 'Formato',

                    'format-options' => [
                        'dymo' => 'Dymo',
                        '2x7_price' => '2x7 com preço',
                        '4x7_price' => '4x7 com preço',
                        '4x12' => '4x12',
                        '4x12_price' => '4x12 com preço',
                    ],
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Produto excluído',
                'body' => 'O produto foi excluído com sucesso.',
            ],
        ],
    ],
];
