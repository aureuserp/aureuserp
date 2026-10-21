<?php

return [
    'title'        => 'Sesión',
    'plural-title' => 'Sesiones',

    'log-attributes' => [
        'name'                  => 'Sesión',
        'state'                 => 'Estado',
        'cash-balance-start'    => 'Saldo de apertura',
        'cash-balance-end-real' => 'Saldo contado',
        'cash-difference'       => 'Diferencia',
    ],

    'chatter' => [
        'opening' => [
            'difference' => 'Diferencia de efectivo de apertura: :amount',
            'expected'   => 'Efectivo de apertura esperado: :amount',
            'counted'    => 'Efectivo de apertura contado: :amount',
            'message'    => 'Mensaje del control de apertura: :message',
        ],

        'closing' => [
            'difference' => 'Diferencia de cierre: :amount',
            'expected'   => 'Cierre esperado: :amount',
            'counted'    => 'Cierre contado: :amount',
            'message'    => 'Mensaje del control de cierre: :message',
        ],

        'cash-movement' => [
            'in'     => 'Entrada de efectivo: :amount',
            'out'    => 'Salida de efectivo: :amount',
            'reason' => 'Motivo: :reason',
        ],
    ],
];
