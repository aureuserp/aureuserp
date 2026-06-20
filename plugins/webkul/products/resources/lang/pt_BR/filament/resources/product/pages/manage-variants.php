<?php

return [
    'title' => 'Variantes',

    'form' => [
        'date' => 'Data',
        'employee' => 'Funcionário',
        'description' => 'Descrição',
        'time-spent' => 'Tempo gasto',
        'time-spent-helper-text' => 'Tempo gasto em horas (ex.: 1,5 hora significa 1 hora e 30 minutos)',
    ],

    'table' => [
        'columns' => [
            'date' => 'Data',
            'employee' => 'Funcionário',
            'description' => 'Descrição',
            'time-spent' => 'Tempo gasto',
            'time-spent-on-subtasks' => 'Tempo gasto em subtarefas',
            'total-time-spent' => 'Tempo total gasto',
            'remaining-time' => 'Tempo restante',
            'variant-values' => 'Valores da variante',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Variante excluída',
                    'body' => 'A variante foi excluída com sucesso.',
                ],
            ],

            'view' => [
                'extra-footer-actions' => [
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
                ],
            ],
        ],
    ],
];
