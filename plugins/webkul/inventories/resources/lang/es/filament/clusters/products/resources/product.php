<?php

return [
    'navigation' => [
        'title' => 'Productos',
        'group' => 'Inventario',
    ],

    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'Inventario',

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Seguimiento',

                        'fields' => [
                            'track-inventory'             => 'Rastrear Inventario',
                            'track-inventory-hint-tooltip' => 'Un producto almacenable es aquel que requiere gestión de inventario.',
                            'track-by'                    => 'Rastrear Por',
                            'expiration-date'             => 'Fecha de Expiración',
                            'expiration-date-hint-tooltip' => 'Si se selecciona, puede especificar fechas de vencimiento para el producto y sus números de lote/serie asociados.',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Operaciones',

                        'fields' => [
                            'routes'                    => 'Rutas',
                            'routes-hint-tooltip'       => 'Según los módulos instalados, esta configuración le permite definir la ruta del producto, como compra, fabricación o reabastecimiento bajo pedido.',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Logística',

                        'fields' => [
                            'responsible'             => 'Responsable',
                            'responsible-hint-tooltip' => 'El plazo de entrega (en días) representa la duración prometida entre la confirmación del pedido de venta y la entrega del producto.',
                            'weight'                  => 'Peso',
                            'volume'                  => 'Volumen',
                            'sale-delay'              => 'Plazo de Entrega al Cliente (Días)',
                            'sale-delay-hint-tooltip' => 'El plazo de entrega (en días) representa la duración prometida entre la confirmación del pedido de venta y la entrega del producto.',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Trazabilidad',

                        'fields' => [
                            'expiration-date'             => 'Fecha de Expiración (Días)',
                            'expiration-date-hint-tooltip' => 'Si se selecciona, puede establecer fechas de vencimiento para el producto y sus números de lote/serie asociados.',
                            'best-before-date'            => 'Fecha de Consumo Preferente (Días)',
                            'best-before-date-hint-tooltip' => 'El número de días antes de la fecha de vencimiento en que el producto comienza a deteriorarse, aunque todavía es seguro usarlo. Esto se calcula en función del lote/número de serie.',
                            'removal-date'                => 'Fecha de Retiro (Días)',
                            'removal-date-hint-tooltip'   => 'El número de días antes de la fecha de vencimiento en que el producto debe retirarse del stock. Esto se calcula en función del lote/número de serie.',
                            'alert-date'                  => 'Fecha de Alerta (Días)',
                            'alert-date-hint-tooltip'     => 'El número de días antes de la fecha de vencimiento en que debe activarse una alerta para el lote/número de serie. Esto se calcula en función del lote/número de serie.',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Adicional',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'inventory' => [
                'title' => 'Inventario',

                'entries' => [
                ],

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Seguimiento',

                        'entries' => [
                            'track-inventory' => 'Rastrear Inventario',
                            'track-by'        => 'Rastrear Por',
                            'expiration-date' => 'Fecha de Expiración',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Operaciones',

                        'entries' => [
                            'routes' => 'Rutas',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Logística',

                        'entries' => [
                            'responsible' => 'Responsable',
                            'weight'      => 'Peso',
                            'volume'      => 'Volumen',
                            'sale-delay'  => 'Plazo de Entrega al Cliente (Días)',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Trazabilidad',

                        'entries' => [
                            'expiration-date' => 'Fecha de Expiración (Días)',
                            'best-before-date' => 'Fecha de Consumo Preferente (Días)',
                            'removal-date'    => 'Fecha de Retiro (Días)',
                            'alert-date'      => 'Fecha de Alerta (Días)',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
