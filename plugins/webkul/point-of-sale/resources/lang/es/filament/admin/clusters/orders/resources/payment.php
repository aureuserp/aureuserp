<?php

return [
    'navigation' => [
        'title' => 'Pagos',
        'group' => 'Punto de venta',
    ],

    'table' => [
        'columns' => [
            'order'           => 'Pedido',
            'session'         => 'Sesión',
            'payment-method'  => 'Método',
            'amount'          => 'Importe',
            'partner'         => 'Cliente',
            'is-change'       => 'Cambio',
            'terminal-status' => 'Estado del terminal',
            'paid-at'         => 'Pagado el',
        ],

        'groups' => [
            'payment-method' => 'Método',
            'session'        => 'Sesión',
        ],

        'filters' => [
            'payment-method' => 'Método',
            'session'        => 'Sesión',
        ],
    ],
];
