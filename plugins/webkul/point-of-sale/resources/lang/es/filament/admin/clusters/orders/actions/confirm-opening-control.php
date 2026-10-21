<?php

return [
    'label'        => 'Abrir sesión',

    'form' => [
        'fields' => [
            'cash-balance-start' => 'Saldo de efectivo de apertura',
            'opening-notes'      => 'Notas',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Sesión abierta',
            'body'  => 'La sesión está ahora en curso.',
        ],
    ],
];
