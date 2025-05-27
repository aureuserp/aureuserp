<?php

return [
    'navigation' => [
        'title' => 'Lotes / Números de Serie',
        'group' => 'Inventario',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'                  => 'Nombre',
                    'name-placeholder'      => 'ej. LOTE/0001/20121',
                    'product'               => 'Producto',
                    'product-hint-tooltip'  => 'El producto asociado con este lote/número de serie. No se puede cambiar si ya ha sido movido.',
                    'reference'             => 'Referencia',
                    'reference-hint-tooltip' => 'Un número de referencia interno, si es diferente del lote/número de serie del fabricante.',
                    'description'           => 'Descripción',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'          => 'Nombre',
            'product'       => 'Producto',
            'on-hand-qty'   => 'Cantidad En Existencia',
            'reference'     => 'Referencia Interna',
            'created-at'    => 'Creado En',
            'updated-at'    => 'Actualizado En',
        ],

        'groups' => [
            'product'     => 'Producto',
            'location'    => 'Ubicación',
            'created-at'  => 'Creado En',
        ],

        'filters' => [
            'product'   => 'Producto',
            'location'  => 'Ubicación',
            'creator'   => 'Creador',
            'company'   => 'Empresa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Lote eliminado',
                        'body'  => 'El lote ha sido eliminado exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el lote',
                        'body'  => 'El lote no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Imprimir Código de Barras',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Lotes eliminados',
                        'body'  => 'Los lotes han sido eliminados exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los lotes',
                        'body'  => 'Los lotes no se pueden eliminar porque están actualmente en uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Detalles del Lote',

                'entries' => [
                    'name'        => 'Nombre del Lote',
                    'product'     => 'Producto',
                    'reference'   => 'Referencia',
                    'description' => 'Descripción',
                    'on-hand-qty' => 'Cantidad En Existencia',
                    'company'     => 'Empresa',
                    'created-at'  => 'Creado En',
                    'updated-at'  => 'Última Actualización',
                ],
            ],

            'record-information' => [
                'title' => 'Información del Registro',

                'entries' => [
                    'created-by'    => 'Creado Por',
                    'created-at'    => 'Creado En',
                    'last-updated'  => 'Última Actualización',
                ],
            ],
        ],
    ],
];
