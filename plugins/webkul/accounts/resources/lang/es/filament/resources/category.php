<?php

return [
    'form' => [
        'fieldsets' => [
            'account-properties' => [
                'label' => 'Propiedades de la cuenta',

                'fields' => [
                    'income-account'                    => 'Cuenta de ingresos',
                    'income-account-hint-tooltip'       => 'Esta cuenta se utilizará al validar una factura de cliente.',
                    'expense-account'                   => 'Cuenta de gastos',
                    'expense-account-hint-tooltip'      => 'El gasto se registra cuando se valida una factura de proveedor, excepto en la contabilidad anglosajona con valoración de inventario perpetua, donde en su lugar se reconoce como el coste de los bienes vendidos cuando se valida la factura de cliente.',
                    'down-payment-account'              => 'Cuenta de anticipos',
                    'down-payment-account-hint-tooltip' => 'Seleccionar la cuenta en la que se registrarán los anticipos de esta categoría.',
                ],
            ],
        ],
    ],
];
