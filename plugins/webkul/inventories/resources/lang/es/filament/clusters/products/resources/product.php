<?php

return [
    'table' => [
        'columns' => [
            'on-hand'    => 'Disponible',
            'forecasted' => 'Previsto',
        ],
    ],

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
                            'track-inventory'              => 'Rastrear inventario',
                            'track-inventory-hint-tooltip' => 'Un producto almacenable es aquel que requiere gestión de inventario.',
                            'track-by'                     => 'Rastrear por',
                            'expiration-date'              => 'Fecha de vencimiento',
                            'expiration-date-hint-tooltip' => 'Si se selecciona, puede especificar fechas de vencimiento para el producto y sus lotes/números de serie asociados.',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Operaciones',

                        'fields' => [
                            'routes'              => 'Rutas',
                            'routes-hint-tooltip' => 'Según los módulos instalados, esta configuración permite definir la ruta del producto, como compra, fabricación o reabastecimiento bajo pedido.',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Logística',

                        'fields' => [
                            'responsible'              => 'Responsable',
                            'responsible-hint-tooltip' => 'El tiempo de entrega (en días) representa la duración prometida entre la confirmación del pedido de venta y la entrega del producto.',
                            'weight'                   => 'Peso',
                            'volume'                   => 'Volumen',
                            'sale-delay'               => 'Tiempo de entrega al cliente (días)',
                            'sale-delay-hint-tooltip'  => 'El tiempo de entrega (en días) representa la duración prometida entre la confirmación del pedido de venta y la entrega del producto.',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Trazabilidad',

                        'fields' => [
                            'expiration-date'               => 'Fecha de vencimiento (días)',
                            'expiration-date-hint-tooltip'  => 'Si se selecciona, puede establecer fechas de vencimiento para el producto y sus lotes/números de serie asociados.',
                            'best-before-date'              => 'Fecha de consumo preferente (días)',
                            'best-before-date-hint-tooltip' => 'El número de días antes de la fecha de vencimiento en que el producto comienza a deteriorarse, aunque aún es seguro usarlo. Se calcula en función del lote/número de serie.',
                            'removal-date'                  => 'Fecha de retiro (días)',
                            'removal-date-hint-tooltip'     => 'El número de días antes de la fecha de vencimiento en que el producto debe retirarse del stock. Se calcula en función del lote/número de serie.',
                            'alert-date'                    => 'Fecha de alerta (días)',
                            'alert-date-hint-tooltip'       => 'El número de días antes de la fecha de vencimiento en que debe generarse una alerta para el lote/número de serie. Se calcula en función del lote/número de serie.',
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
                            'track-inventory' => 'Rastrear inventario',
                            'track-by'        => 'Rastrear por',
                            'expiration-date' => 'Fecha de vencimiento',
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
                            'sale-delay'  => 'Tiempo de entrega al cliente (días)',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Trazabilidad',

                        'entries' => [
                            'expiration-date'  => 'Fecha de vencimiento (días)',
                            'best-before-date' => 'Fecha de consumo preferente (días)',
                            'removal-date'     => 'Fecha de retiro (días)',
                            'alert-date'       => 'Fecha de alerta (días)',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
