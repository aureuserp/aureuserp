<?php

return [
    'form' => [
        'fields' => [
            'name'               => 'Nombre',
            'rounding-precision' => 'Precisión de redondeo',
            'rounding-strategy'  => 'Estrategia de redondeo',
            'profit-account'     => 'Cuenta de beneficios',
            'loss-account'       => 'Cuenta de pérdidas',
            'rounding-method'    => 'Método de redondeo',
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Nombre',
            'rounding-strategy'    => 'Estrategia de redondeo',
            'rounding-method'      => 'Método de redondeo',
            'created-by'           => 'Creado por',
            'profit-account'       => 'Cuenta de beneficios',
            'loss-account'         => 'Cuenta de pérdidas',
        ],

        'groups' => [
            'name'              => 'Nombre',
            'rounding-strategy' => 'Estrategia de redondeo',
            'rounding-method'   => 'Método de redondeo',
            'created-by'        => 'Creado por',
            'profit-account'    => 'Cuenta de beneficios',
            'loss-account'      => 'Cuenta de pérdidas',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Redondeo de efectivo eliminado',
                    'body'  => 'El redondeo de efectivo se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Redondeo de efectivo eliminado',
                    'body'  => 'El redondeo de efectivo se ha eliminado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'               => 'Nombre',
            'rounding-precision' => 'Precisión de redondeo',
            'rounding-strategy'  => 'Estrategia de redondeo',
            'profit-account'     => 'Cuenta de beneficios',
            'loss-account'       => 'Cuenta de pérdidas',
            'rounding-method'    => 'Método de redondeo',
        ],
    ],
];
