<?php

return [
    'form' => [
        'factor-percent'    => 'Porcentaje del factor',
        'factor-ratio'      => 'Proporción del factor',
        'repartition-type'  => 'Tipo de reparto',
        'document-type'     => 'Tipo de documento',
        'account'           => 'Cuenta',
        'tax'               => 'Impuesto',
        'tax-closing-entry' => 'Asiento de cierre de impuestos',
    ],

    'table' => [
        'columns' => [
            'factor-percent'    => 'Porcentaje del factor (%)',
            'account'           => 'Cuenta',
            'tax'               => 'Impuesto',
            'company'           => 'Empresa',
            'repartition-type'  => 'Tipo de reparto',
            'document-type'     => 'Tipo de documento',
            'tax-closing-entry' => 'Asiento de cierre de impuestos',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Reparto de impuestos actualizado',
                    'body'  => 'El reparto de impuestos se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Reparto de impuestos eliminado',
                    'body'  => 'El reparto de impuestos se ha eliminado correctamente.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Reparto de impuestos creado',
                    'body'  => 'El reparto de impuestos se ha creado correctamente.',
                ],
            ],
        ],
    ],
];
