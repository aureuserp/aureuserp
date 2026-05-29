<?php

return [
    'navigation' => [
        'title' => 'Lotes / Números de serie',
        'group' => 'Inventario',
    ],

    'global-search' => [
        'ref'     => 'Referencia',
        'product' => 'Producto',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'                   => 'Nombre',
                    'name-placeholder'       => 'p. ej. LOT/0001/20121',
                    'product'                => 'Producto',
                    'product-hint-tooltip'   => 'El producto asociado a este lote/número de serie. No se puede cambiar si ya ha sido movido.',
                    'reference'              => 'Referencia',
                    'reference-hint-tooltip' => 'Un número de referencia interno, si es diferente al lote/número de serie del fabricante.',
                    'description'            => 'Descripción',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nombre',
            'product'      => 'Producto',
            'on-hand-qty'  => 'Cantidad disponible',
            'reference'    => 'Referencia interna',
            'created-at'   => 'Creado el',
            'updated-at'   => 'Actualizado el',
        ],

        'groups' => [
            'product'        => 'Producto',
            'location'       => 'Ubicación',
            'created-at'     => 'Creado el',
        ],

        'filters' => [
            'product'  => 'Producto',
            'location' => 'Ubicación',
            'creator'  => 'Creador',
            'company'  => 'Empresa',
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
                        'body'  => 'El lote no puede eliminarse porque está en uso actualmente.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Imprimir código de barras',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Lotes eliminados',
                        'body'  => 'Los lotes han sido eliminados exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los lotes',
                        'body'  => 'Los lotes no pueden eliminarse porque están en uso actualmente.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Detalles del lote',

                'entries' => [
                    'name'        => 'Nombre del lote',
                    'product'     => 'Producto',
                    'reference'   => 'Referencia',
                    'description' => 'Descripción',
                    'on-hand-qty' => 'Cantidad disponible',
                    'company'     => 'Empresa',
                    'created-at'  => 'Creado el',
                    'updated-at'  => 'Última actualización',
                ],
            ],

            'record-information' => [
                'title' => 'Información del registro',

                'entries' => [
                    'created-by'   => 'Creado por',
                    'created-at'   => 'Creado el',
                    'last-updated' => 'Última actualización',
                ],
            ],
        ],
    ],
];
