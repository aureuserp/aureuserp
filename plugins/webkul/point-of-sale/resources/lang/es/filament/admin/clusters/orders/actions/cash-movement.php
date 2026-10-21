<?php

return [
    'label'        => 'Entrada/Salida de efectivo',

    'form' => [
        'fields' => [
            'type'   => 'Tipo',
            'amount' => 'Importe',
            'reason' => 'Motivo',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Movimiento de efectivo registrado',
            'body'  => 'El saldo del cajón de efectivo se ha actualizado.',
        ],
    ],
];
