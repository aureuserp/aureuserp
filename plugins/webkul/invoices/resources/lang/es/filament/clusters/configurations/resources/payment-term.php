<?php

return [
    'title' => 'Condiciones de Pago',

    'navigation' => [
        'title' => 'Condiciones de Pago',
        'group' => 'Facturación',
    ],

    'global-search' => [
        'company-name' => 'Nombre de la Empresa',
        'payment-term' => 'Condición de Pago',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'payment-term'        => 'Condición de Pago',
                'early-discount'      => 'Descuento por Pronto Pago',
                'discount-days-prefix' => 'si se paga dentro de',
                'discount-days-suffix' => 'días',
                'reduced-tax'         => 'Impuesto Reducido',
                'note'                => 'Nota',
                'status'              => 'Estado',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'payment-term'        => 'Condición de Pago',
            'company'             => 'Empresa',
            'discount-days'       => 'Días de Descuento',
            'early-pay-discount'  => 'Descuento por Pronto Pago',
            'status'              => 'Estado',
            'early-discount'      => 'Descuento por Pronto Pago',
            'display-on-invoice'  => 'Mostrar en Factura',
            'created-by'          => 'Creado Por',
            'created-at'          => 'Creado En',
            'updated-at'          => 'Actualizado En',
        ],

        'groups' => [
            'payment-term'        => 'Condición de Pago',
            'company-name'        => 'Nombre de la Empresa',
            'discount-days'       => 'Días de Descuento',
            'early-pay-discount'  => 'Descuento por Pronto Pago',
            'display-on-invoice'  => 'Mostrar en Factura',
            'early-discount'      => 'Descuento por Pronto Pago',
            'discount-percentage' => 'Porcentaje de Descuento',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Condición de Pago restaurada',
                    'body'  => 'La condición de pago ha sido restaurada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Condición de Pago eliminada',
                    'body'  => 'La condición de pago ha sido eliminada exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Condición de Pago eliminada permanentemente',
                    'body'  => 'La condición de pago ha sido eliminada permanentemente exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Condiciones de Pago restauradas',
                    'body'  => 'Las condiciones de pago han sido restauradas exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Condiciones de Pago eliminadas',
                    'body'  => 'Las condiciones de pago han sido eliminadas exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Condiciones de Pago eliminadas permanentemente',
                    'body'  => 'Las condiciones de pago han sido eliminadas permanentemente exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'payment-term'        => 'Condición de Pago',
                'early-discount'      => 'Descuento por Pronto Pago',
                'discount-percentage' => 'Porcentaje de Descuento',
                'discount-days-prefix' => 'si se paga dentro de',
                'discount-days-suffix' => 'días',
                'reduced-tax'         => 'Impuesto Reducido',
                'note'                => 'Nota',
                'status'              => 'Estado',
            ],
        ],
    ],
];
