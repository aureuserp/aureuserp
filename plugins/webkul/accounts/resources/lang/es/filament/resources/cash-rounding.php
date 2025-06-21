<?php

return [
    'form' => [
        'fields' => [
            'name'               => 'Nombre',
            'rounding-precision' => 'Precisión de Redondeo',
            'rounding-strategy'  => 'Estrategia de Redondeo',
            'profit-account'     => 'Cuenta de Ganancias',
            'loss-account'       => 'Cuenta de Pérdidas',
            'rounding-method'    => 'Método de Redondeo',
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Nombre',
            'rounding-strategy'    => 'Estrategia de Redondeo',
            'rounding-method'      => 'Método de Redondeo',
            'created-by'           => 'Creado Por',
            'profit-account'       => 'Cuenta de Ganancias',
            'loss-account'         => 'Cuenta de Pérdidas',
        ],

        'groups' => [
            'name'              => 'Nombre',
            'rounding-strategy' => 'Estrategia de Redondeo',
            'rounding-method'   => 'Método de Redondeo',
            'created-by'        => 'Creado Por',
            'profit-account'    => 'Cuenta de Ganancias',
            'loss-account'      => 'Cuenta de Pérdidas',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Redondeo de efectivo eliminado',
                    'body'  => 'El redondeo de efectivo ha sido eliminado exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Redondeos de efectivo eliminados',
                    'body'  => 'Los redondeos de efectivo han sido eliminados exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'               => 'Nombre',
            'rounding-precision' => 'Precisión de Redondeo',
            'rounding-strategy'  => 'Estrategia de Redondeo',
            'profit-account'     => 'Cuenta de Ganancias',
            'loss-account'       => 'Cuenta de Pérdidas',
            'rounding-method'    => 'Método de Redondeo',
        ],
    ],
];
