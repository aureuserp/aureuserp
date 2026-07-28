<?php

return [
    'title' => 'Fatura',

    'navigation' => [
        'title' => 'Faturas',
    ],

    'global-search' => [
        'customer' => 'Cliente',
        'date' => 'Data',
        'due-date' => 'Data de vencimento',
        'amount' => 'Valor',
    ],

    'form' => [
        'tabs' => [
            'invoice-lines' => [
                'repeater' => [
                    'products' => [
                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Abrir produto',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
