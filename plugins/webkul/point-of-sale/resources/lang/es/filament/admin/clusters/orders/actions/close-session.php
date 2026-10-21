<?php

return [
    'label'        => 'Cerrar sesión',

    'form' => [
        'fields' => [
            'cash-balance-end-real'             => 'Efectivo contado',
            'cash-balance-end-real-helper-text' => 'Saldo esperado: :expected',
            'closing-notes'                     => 'Notas',
            'balancing-account'                 => 'Cuenta de ajuste',
            'balancing-account-helper-text'     => 'Solo se usa cuando el asiento de cierre no cuadra.',
        ],
    ],

    'notification' => [
        'unbalanced' => [
            'title' => 'Asiento de cierre descuadrado',
        ],

        'success' => [
            'title' => 'Sesión cerrada',
            'body'  => 'La sesión se ha cerrado.',
        ],
    ],
];
