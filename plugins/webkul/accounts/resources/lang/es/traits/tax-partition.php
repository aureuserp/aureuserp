<?php

return [
    'form' => [
        'factor-percent'    => 'Factor Porcentual',
        'factor-ratio'      => 'Factor de Ratio',
        'repartition-type'  => 'Tipo de Repartición',
        'document-type'     => 'Tipo de Documento',
        'account'           => 'Cuenta',
        'tax'               => 'Impuesto',
        'tax-closing-entry' => 'Asiento de Cierre de Impuesto',
    ],

    'table' => [
        'columns' => [
            'factor-percent'    => 'Factor Porcentual (%)',
            'account'           => 'Cuenta',
            'tax'               => 'Impuesto',
            'company'           => 'Empresa',
            'repartition-type'  => 'Tipo de Repartición',
            'document-type'     => 'Tipo de Documento',
            'tax-closing-entry' => 'Asiento de Cierre de Impuesto',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Partición de Impuestos actualizada',
                    'body'  => 'La partición de impuestos ha sido actualizada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Término de Partición de Impuestos eliminado',
                    'body'  => 'El término de partición de impuestos ha sido eliminado exitosamente.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Término de Partición de Impuestos creado',
                    'body'  => 'El término de partición de impuestos ha sido creado exitosamente.',
                ],
            ],
        ],
    ],
];
