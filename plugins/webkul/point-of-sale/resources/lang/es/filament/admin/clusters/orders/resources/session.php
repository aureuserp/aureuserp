<?php

return [

    'navigation' => [
        'title' => 'Sesiones',
        'group' => 'Punto de venta',
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'Sesión',
                'entries' => [
                    'session'          => 'Sesión',
                    'opened-by'        => 'Abierta por',
                    'point-of-sale'    => 'Punto de venta',
                    'opening-date'     => 'Fecha de apertura',
                    'starting-balance' => 'Saldo inicial',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                  => 'ID de sesión',
            'config'                => 'Punto de venta',
            'user'                  => 'Abierta por',
            'started-at'            => 'Fecha de apertura',
            'stopped-at'            => 'Fecha de cierre',
            'cash-balance-start'    => 'Saldo inicial',
            'cash-balance-end-real' => 'Saldo final',
            'cash-balance-end'      => 'Saldo final teórico',
            'order-count'           => 'Pedidos',
            'total-payments-amount' => 'Pagos',
            'cash-difference'       => 'Diferencia',
            'is-rescue'             => 'Rescate',
            'has-failed-operations' => 'Operaciones fallidas',
            'state'                 => 'Estado',
            'company'               => 'Empresa',
        ],

        'groups' => [
            'config'     => 'Punto de venta',
            'state'      => 'Estado',
            'started-at' => 'Abierta el',
        ],

        'filters' => [
            'state'  => 'Estado',
            'config' => 'Punto de venta',
        ],
    ],
];
