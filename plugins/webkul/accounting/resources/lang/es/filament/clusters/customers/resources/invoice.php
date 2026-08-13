<?php

return [
    'title' => 'Factura',

    'navigation' => [
        'title' => 'Facturas',
    ],

    'global-search' => [
        'customer' => 'Cliente',
        'date'     => 'Fecha',
        'due-date' => 'Fecha de vencimiento',
        'amount'   => 'Importe',
    ],

    'form' => [
        'tabs' => [
            'invoice-lines' => [
                'repeater' => [
                    'products' => [
                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Abrir producto',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
