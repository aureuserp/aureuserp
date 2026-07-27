<?php

return [
    'title' => 'Pagar',

    'form' => [
        'fields' => [
            'journal'              => 'Diario',
            'amount'               => 'Importe',
            'currency'             => 'Moneda',
            'payment-method-line'  => 'Línea de método de pago',
            'payment-date'         => 'Fecha de pago',
            'partner-bank-account' => 'Cuenta bancaria del contacto',
            'communication'        => 'Nota',
        ],
    ],

    'notifications' => [
        'payment-failed' => [
            'title' => 'Pago fallido',
        ],
    ],
];
