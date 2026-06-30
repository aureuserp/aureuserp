<?php

return [
    'assets' => [
        'label'   => 'Activos',
        'options' => [
            'receivable'  => 'Por cobrar',
            'cash'        => 'Banco y efectivo',
            'current'     => 'Activos corrientes',
            'non-current' => 'Activos no corrientes',
            'prepayments' => 'Pagos anticipados',
            'fixed'       => 'Activos fijos',
        ],
    ],

    'liabilities' => [
        'label'   => 'Pasivos',
        'options' => [
            'payable'     => 'Por pagar',
            'credit-card' => 'Tarjeta de crédito',
            'current'     => 'Pasivos corrientes',
            'non-current' => 'Pasivos no corrientes',
        ],
    ],

    'equity' => [
        'label'   => 'Patrimonio',
        'options' => [
            'equity'     => 'Patrimonio',
            'unaffected' => 'Resultados del ejercicio actual',
        ],
    ],

    'income' => [
        'label'   => 'Ingresos',
        'options' => [
            'income' => 'Ingresos',
            'other'  => 'Otros ingresos',
        ],
    ],

    'expenses' => [
        'label'   => 'Gastos',
        'options' => [
            'expense'      => 'Gastos',
            'depreciation' => 'Depreciación',
            'direct-cost'  => 'Costo de ventas',
        ],
    ],

    'off-balance' => [
        'label'   => 'Fuera de balance',
        'options' => [
            'off-balance' => 'Fuera de balance',
        ],
    ],
];
