<?php

return [
    'navigation' => [
        'title' => 'Órdenes de fabricación',
        'group' => 'Operaciones',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'product'                => 'Producto',
                    'quantity'               => 'Cantidad',
                    'uom'                    => 'UoM',
                    'bill-of-material'       => 'Lista de materiales',
                    'scheduled-date'         => 'Fecha programada',
                    'scheduled-end'          => 'Fin programado',
                    'responsible'            => 'Responsable',
                    'to-produce'             => 'A producir',
                    'to-produce-placeholder' => 'Vista previa de imagen',
                    'uom-placeholder'        => 'UoM',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'        => 'Componentes',
                'add-action'   => 'Agregar una línea',
                'process-note' => 'Los componentes se generarán conforme se construya el proceso de fabricación.',
                'columns'      => [
                    'component'          => 'Producto',
                    'from'               => 'Desde',
                    'to-consume'         => 'A consumir',
                    'to-consume-tooltip' => 'Cantidad disponible insuficiente',
                    'quantity'           => 'Cantidad',
                    'uom'                => 'UoM',
                    'forecast'           => 'Previsión',
                ],
            ],
            'work-orders' => [
                'title'        => 'Órdenes de trabajo',
                'add-action'   => 'Agregar una línea',
                'process-note' => 'Las órdenes de trabajo se generarán después de configurar el proceso de fabricación.',
                'columns'      => [
                    'operation'          => 'Operación',
                    'work-center'        => 'Centro de trabajo',
                    'product'            => 'Producto',
                    'quantity-remaining' => 'Cantidad restante',
                    'quantity-produced'  => 'Cantidad producida',
                    'start'              => 'Inicio',
                    'end'                => 'Fin',
                    'expected-duration'  => 'Duración esperada',
                    'real-duration'      => 'Duración real',
                    'status'             => 'Estado',
                    'lot-serial'         => 'Lote/Serie',
                ],
                'actions'      => [
                    'open-work-order' => [
                        'tooltip' => 'Abrir orden de trabajo',
                    ],

                    'done' => [
                        'label' => 'Hecho',
                    ],
                ],
            ],
            'by-products' => [
                'title'        => 'Subproductos',
                'process-note' => 'Los subproductos se generarán conforme se construya el proceso de fabricación.',
                'columns'      => [
                    'product'    => 'Producto',
                    'to'         => 'Hacia',
                    'to-produce' => 'A producir',
                    'uom'        => 'UoM',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Varios',
                'fields' => [
                    'operation-type'             => 'Tipo de operación',
                    'source'                     => 'Origen',
                    'finished-products-location' => 'Ubicación de productos terminados',
                    'company'                    => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference'              => 'Referencia',
            'start'                  => 'Inicio',
            'end'                    => 'Fin',
            'deadline'               => 'Fecha límite',
            'product'                => 'Producto',
            'lot-serial-number'      => 'Número de lote/serie',
            'bill-of-material'       => 'Lista de materiales',
            'source'                 => 'Origen',
            'responsible'            => 'Responsable',
            'mo-readiness'           => 'Disponibilidad OF',
            'component-status'       => 'Estado de componentes',
            'quantity'               => 'Cantidad',
            'uom'                    => 'UoM',
            'consumption-efficiency' => 'Eficiencia de consumo',
            'expected-duration'      => 'Duración esperada',
            'real-duration'          => 'Duración real',
            'company'                => 'Empresa',
            'state'                  => 'Estado',
        ],
        'groups' => [
            'state'            => 'Estado',
            'product'          => 'Producto',
            'bill-of-material' => 'Lista de materiales',
            'responsible'      => 'Responsable',
            'deadline'         => 'Fecha límite',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'General',
                'entries' => [
                    'product'                  => 'Producto',
                    'scheduled-date'           => 'Fecha programada',
                    'responsible'              => 'Responsable',
                    'quantity'                 => 'Cantidad',
                    'uom'                      => 'UoM',
                    'bill-of-material'         => 'Lista de materiales',
                    'operation-type'           => 'Tipo de operación',
                    'consumption-efficiency'   => 'Eficiencia de consumo',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'        => 'Componentes',
                'process-note' => 'Los componentes estarán disponibles después de configurar el proceso de fabricación.',
                'columns'      => [
                    'component' => 'Componente',
                    'quantity'  => 'Cantidad',
                    'uom'       => 'UoM',
                ],
            ],
            'work-orders' => [
                'title'        => 'Órdenes de trabajo',
                'process-note' => 'Las órdenes de trabajo estarán disponibles después de configurar el proceso de fabricación.',
                'columns'      => [
                    'operation'          => 'Operación',
                    'work-center'        => 'Centro de trabajo',
                    'product'            => 'Producto',
                    'quantity-remaining' => 'Cantidad restante',
                    'expected-duration'  => 'Duración esperada',
                    'real-duration'      => 'Duración real',
                    'lot-serial'         => 'Lote/Serie',
                    'start'              => 'Inicio',
                    'end'                => 'Fin',
                ],
            ],
            'by-products' => [
                'title'        => 'Subproductos',
                'process-note' => 'Los subproductos estarán disponibles después de configurar el proceso de fabricación.',
                'columns'      => [
                    'product'    => 'Producto',
                    'to'         => 'Hacia',
                    'to-produce' => 'A producir',
                    'uom'        => 'UoM',
                ],
            ],
            'miscellaneous' => [
                'title'   => 'Varios',
                'entries' => [
                    'operation-type'             => 'Tipo de operación',
                    'source'                     => 'Origen',
                    'finished-products-location' => 'Ubicación de productos terminados',
                    'company'                    => 'Empresa',
                ],
            ],
        ],
    ],

    'pages' => [
        'shared' => [
            'header-actions' => [
                'confirm' => [
                    'label'        => 'Confirmar',
                    'notification' => [
                        'title' => 'Orden de fabricación confirmada',
                    ],
                ],

                'cancel' => [
                    'label'        => 'Cancelar',
                    'notification' => [
                        'title' => 'Orden de fabricación cancelada',
                    ],
                ],
            ],
        ],
    ],
];
