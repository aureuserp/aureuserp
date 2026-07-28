<?php

return [
    'title' => 'Gestionar cuentas predeterminadas',

    'form' => [
        'exchange-difference-entries' => [
            'label' => 'Asientos de diferencia de cambio',

            'fields' => [
                'journal' => [
                    'label' => 'Diario',
                ],

                'gain' => [
                    'label' => 'Ganancia',
                ],

                'loss' => [
                    'label' => 'Pérdida',
                ],
            ],
        ],

        'bank-transfer-and-payments' => [
            'label' => 'Transferencias y pagos bancarios',

            'fields' => [
                'bank-suspense-account' => [
                    'label' => 'Cuenta transitoria bancaria',
                ],

                'transfer-account' => [
                    'label' => 'Cuenta de transferencia',
                ],
            ],
        ],

        'product-accounts' => [
            'label' => 'Cuentas de producto',

            'fields' => [
                'income-account' => [
                    'label' => 'Cuenta de ingresos',
                ],

                'expense-account' => [
                    'label' => 'Cuenta de gastos',
                ],
            ],
        ],
    ],
];
