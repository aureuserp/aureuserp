<?php

return [
    'navigation' => [
        'title' => 'Órdenes de trabajo',
        'group' => 'Operaciones',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'work-order'          => 'Orden de trabajo',
                    'work-center'         => 'Centro de trabajo',
                    'product'             => 'Producto',
                    'quantity'            => 'Cantidad',
                    'manufacturing-order' => 'Orden de fabricación',
                    'lot-serial'          => 'Número de lote/serie',
                    'start-date'          => 'Fecha de inicio',
                    'end-date'            => 'Fecha de finalización',
                    'date-range-separator'=> 'a',
                    'expected-duration'   => 'Duración esperada',
                    'duration-suffix'     => 'minutos',
                    'real-duration'       => 'Duración real',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title'      => 'Seguimiento de tiempo',
                'add-action' => 'Agregar una línea',
                'columns'    => [
                    'user'         => 'Usuario',
                    'duration'     => 'Duración',
                    'start-date'   => 'Fecha de inicio',
                    'end-date'     => 'Fecha de finalización',
                    'productivity' => 'Productividad',
                ],
                'footer' => [
                    'real-duration' => 'Duración real',
                ],
            ],
            'components' => [
                'title'      => 'Componentes',
                'add-action' => 'Agregar una línea',
                'columns'    => [
                    'product'    => 'Producto',
                    'from' => 'Desde',
                    'to-consume' => 'A consumir',
                    'quantity'   => 'Cantidad',
                    'uom'        => 'UoM',
                ],
            ],
            'work-instruction' => [
                'title'   => 'Instrucción de trabajo',
                'entries' => [
                    'operation' => 'Operación',
                    'worksheet' => 'Hoja de trabajo',
                ],
            ],
            'blocked-by' => [
                'title'  => 'Bloqueado por',
                'fields' => [
                    'work-orders' => 'Órdenes de trabajo',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'operation'           => 'Operación',
            'work-center'         => 'Centro de trabajo',
            'manufacturing-order' => 'Orden de fabricación',
            'product'             => 'Producto',
            'quantity-remaining'  => 'Cantidad restante',
            'lot-serial'          => 'Lote/Serie',
            'start'               => 'Inicio',
            'end'                 => 'Fin',
            'expected-duration'   => 'Duración esperada',
            'real-duration'       => 'Duración real',
            'status'              => 'Estado',
        ],
        'groups' => [
            'status'              => 'Estado',
            'work-center'         => 'Centro de trabajo',
            'manufacturing-order' => 'Orden de fabricación',
            'product'             => 'Producto',
            'start'               => 'Inicio',
            'end'                 => 'Fin',
        ],
        'filters' => [
            'work-order'          => 'Orden de trabajo',
            'status'              => 'Estado',
            'operation'           => 'Operación',
            'work-center'         => 'Centro de trabajo',
            'manufacturing-order' => 'Orden de fabricación',
            'product'             => 'Producto',
            'start'               => 'Inicio',
            'end'                 => 'Fin',
            'created-at'          => 'Creado el',
            'updated-at'          => 'Actualizado el',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'General',
                'entries' => [
                    'work-order'          => 'Orden de trabajo',
                    'work-center'         => 'Centro de trabajo',
                    'product'             => 'Producto',
                    'quantity'            => 'Cantidad',
                    'manufacturing-order' => 'Orden de fabricación',
                    'lot-serial'          => 'Número de lote/serie',
                    'start-date'          => 'Fecha de inicio',
                    'end-date'            => 'Fecha de finalización',
                    'expected-duration'   => 'Duración esperada',
                    'real-duration'       => 'Duración real',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title'  => 'Seguimiento de tiempo',
                'footer' => [
                    'real-duration' => 'Duración real',
                ],
            ],
            'components' => [
                'title' => 'Componentes',
            ],
            'work-instruction' => [
                'title'   => 'Instrucción de trabajo',
                'entries' => [
                    'operation' => 'Operación',
                    'worksheet' => 'Hoja de trabajo',
                ],
            ],
            'blocked-by' => [
                'title'   => 'Bloqueado por',
                'columns' => [
                    'work-order'  => 'Orden de trabajo',
                    'work-center' => 'Centro de trabajo',
                    'status'      => 'Estado',
                ],
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label' => 'Nueva orden de trabajo',
                ],
            ],
        ],
    ],
];
