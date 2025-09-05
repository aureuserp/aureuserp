<?php

return [
    'form' => [
        'name'    => 'Nombre',
        'barcode' => 'Código de Barras',
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
            'barcode'    => 'Código de Barras',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'groups' => [
            'product'    => 'Producto',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'filters' => [
            'product' => 'Producto',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Embalaje actualizado',
                    'body'  => 'El embalaje ha sido actualizado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Embalaje eliminado',
                        'body'  => 'El embalaje ha sido eliminado exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el embalaje',
                        'body'  => 'El embalaje no puede ser eliminado porque está actualmente en uso.',
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
                        'title' => 'Embalajes eliminados',
                        'body'  => 'Los embalajes han sido eliminados exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los embalajes',
                        'body'  => 'Los embalajes no pueden ser eliminados porque están actualmente en uso.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'Nuevo Embalaje',

                'notification' => [
                    'title' => 'Embalaje creado',
                    'body'  => 'El embalaje ha sido creado exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Información General',

                'entries' => [
                    'name'    => 'Nombre del Paquete',
                    'barcode' => 'Código de Barras',
                    'product' => 'Producto',
                    'qty'     => 'Cantidad',
                ],
            ],

            'organization' => [
                'title' => 'Detalles de la Organización',

                'entries' => [
                    'company'    => 'Empresa',
                    'creator'    => 'Creado Por',
                    'created_at' => 'Creado El',
                    'updated_at' => 'Última Actualización El',
                ],
            ],
        ],
    ],
];
