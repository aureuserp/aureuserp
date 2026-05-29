<?php

return [
    'form' => [
        'name'    => 'Nombre',
        'barcode' => 'Código de barras',
        'product' => 'Producto',
        'routes'  => 'Rutas',
        'qty'     => 'Cantidad',
        'company' => 'Empresa',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'product'    => 'Producto',
            'routes'     => 'Rutas',
            'qty'        => 'Cantidad',
            'company'    => 'Empresa',
            'barcode'    => 'Código de barras',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'groups' => [
            'product'    => 'Producto',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'filters' => [
            'product' => 'Producto',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Empaque actualizado',
                    'body'  => 'El empaque ha sido actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Empaque eliminado',
                        'body'  => 'El empaque ha sido eliminado correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el empaque',
                        'body'  => 'El empaque no puede eliminarse porque está en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Imprimir',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Empaques eliminados',
                        'body'  => 'Los empaques han sido eliminados correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los empaques',
                        'body'  => 'Los empaques no pueden eliminarse porque están en uso.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'Nuevo empaque',

                'notification' => [
                    'title' => 'Empaque creado',
                    'body'  => 'El empaque ha sido creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Información general',

                'entries' => [
                    'name'    => 'Nombre del paquete',
                    'barcode' => 'Código de barras',
                    'product' => 'Producto',
                    'qty'     => 'Cantidad',
                ],
            ],

            'organization' => [
                'title' => 'Detalles de la organización',

                'entries' => [
                    'company'    => 'Empresa',
                    'creator'    => 'Creado por',
                    'created_at' => 'Creado el',
                    'updated_at' => 'Última actualización',
                ],
            ],
        ],
    ],
];
