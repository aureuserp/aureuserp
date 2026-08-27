<?php

return [
    'title' => 'Pagar',

    'form' => [
        'fields' => [
            'journal'                => 'Diario',
            'amount'                 => 'Importe',
            'currency'               => 'Moneda',
            'payment-method-line'    => 'Línea de método de pago',
            'payment-date'           => 'Fecha de pago',
            'recipient-bank-account' => 'Cuenta bancaria del destinatario',
            'communication'          => 'Nota',
        ],
    ],

    'notifications' => [
        'payment-failed' => [
            'title' => 'Pago fallido',
        ],
    ],
];
