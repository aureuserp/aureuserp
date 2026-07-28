<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'payment-term'         => 'Condición de pago',
                'early-discount'       => 'Descuento por pronto pago',
                'discount-days-prefix' => 'si se paga dentro de',
                'discount-days-suffix' => 'días',
                'reduced-tax'          => 'Impuesto reducido',
                'note'                 => 'Nota',
                'status'               => 'Estado',
            ],
        ],

        'tabs' => [
            'due-terms' => [
                'title' => 'Plazos de vencimiento',

                'repeater' => [
                    'due-terms' => [
                        'fields' => [
                            'value'                  => 'Valor',
                            'due'                    => 'Vencimiento',
                            'delay-type'             => 'Tipo de retraso',
                            'days-on-the-next-month' => 'Días del mes siguiente',
                            'days'                   => 'Días',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'payment-term' => 'Condición de pago',
            'company'      => 'Empresa',
            'created-at'   => 'Creado el',
            'updated-at'   => 'Actualizado el',
        ],

        'groups' => [
            'company-name'        => 'Nombre de la empresa',
            'discount-days'       => 'Días de descuento',
            'early-pay-discount'  => 'Descuento por pronto pago',
            'payment-term'        => 'Condición de pago',
            'display-on-invoice'  => 'Mostrar en la factura',
            'early-discount'      => 'Descuento por pronto pago',
            'discount-percentage' => 'Porcentaje de descuento',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Condición de pago restaurada',
                    'body'  => 'La condición de pago se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Condición de pago eliminada',
                    'body'  => 'La condición de pago se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Condición de pago eliminada permanentemente',
                        'body'  => 'La condición de pago se ha eliminado permanentemente correctamente.',
                    ],

                    'error' => [
                        'title' => 'Error al eliminar permanentemente la condición de pago',
                        'body'  => 'La condición de pago no se pudo eliminar permanentemente porque tiene asientos contables asociados.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Condiciones de pago restauradas',
                    'body'  => 'Las condiciones de pago se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Condiciones de pago eliminadas',
                    'body'  => 'Las condiciones de pago se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Condiciones de pago eliminadas permanentemente',
                        'body'  => 'Las condiciones de pago se han eliminado permanentemente correctamente.',
                    ],

                    'error' => [
                        'title' => 'Error al eliminar permanentemente las condiciones de pago',
                        'body'  => 'Las condiciones de pago no se pudieron eliminar permanentemente porque tienen asientos contables asociados.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'payment-term'         => 'Condición de pago',
                'early-discount'       => 'Descuento por pronto pago',
                'discount-percentage'  => 'Porcentaje de descuento',
                'discount-days-prefix' => 'si se paga dentro de',
                'discount-days-suffix' => 'días',
                'reduced-tax'          => 'Impuesto reducido',
                'note'                 => 'Nota',
                'status'               => 'Estado',
            ],
        ],

        'tabs' => [
            'due-terms' => [
                'title' => 'Plazos de vencimiento',

                'repeater' => [
                    'due-terms' => [
                        'entries' => [
                            'value'                  => 'Valor',
                            'due'                    => 'Vencimiento',
                            'delay-type'             => 'Tipo de retraso',
                            'days-on-the-next-month' => 'Días del mes siguiente',
                            'days'                   => 'Días',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
