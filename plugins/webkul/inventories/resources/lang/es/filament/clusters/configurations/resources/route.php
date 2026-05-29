<?php

return [
    'navigation' => [
        'title' => 'Rutas',
        'group' => 'Gestión de almacenes',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'route'             => 'Ruta',
                    'route-placeholder' => 'ej. Recepción en dos pasos',
                    'company'           => 'Empresa',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Aplicable a',
                'description' => 'Elige las ubicaciones donde se puede aplicar esta ruta.',

                'fields' => [
                    'products'                        => 'Productos',
                    'products-hint-tooltip'           => 'Si se selecciona, esta ruta estará disponible para elegirla en el producto.',
                    'product-categories'              => 'Categorías de productos',
                    'product-categories-hint-tooltip' => 'Si se selecciona, esta ruta estará disponible para elegirla en la categoría de producto.',
                    'warehouses'                      => 'Almacenes',
                    'warehouses-hint-tooltip'         => 'Cuando se asigna un almacén a esta ruta, se considerará la ruta predeterminada para los productos que pasen por ese almacén.',
                    'packaging'                       => 'Empaque',
                    'packaging-hint-tooltip'          => 'Si se selecciona, esta ruta estará disponible para elegirla en el empaque.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'route'      => 'Ruta',
            'company'    => 'Empresa',
            'deleted-at' => 'Eliminado el',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'groups' => [
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'filters' => [
            'company'  => 'Empresa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Ruta actualizada',
                    'body'  => 'La ruta ha sido actualizada correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Ruta restaurada',
                    'body'  => 'La ruta ha sido restaurada correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Ruta eliminada',
                    'body'  => 'La ruta ha sido eliminada correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ruta eliminada de forma permanente',
                        'body'  => 'La ruta ha sido eliminada de forma permanente correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar la ruta',
                        'body'  => 'La ruta no puede eliminarse porque está en uso actualmente.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Rutas restauradas',
                    'body'  => 'Las rutas han sido restauradas correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Rutas eliminadas',
                    'body'  => 'Las rutas han sido eliminadas correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Rutas eliminadas de forma permanente',
                        'body'  => 'Las rutas han sido eliminadas de forma permanente correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar las rutas',
                        'body'  => 'Las rutas no pueden eliminarse porque están en uso actualmente.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'entries' => [
                    'route'             => 'Ruta',
                    'route-placeholder' => 'ej. Recepción en dos pasos',
                    'company'           => 'Empresa',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Aplicable a',
                'description' => 'Selecciona los lugares donde se puede seleccionar esta ruta.',

                'entries' => [
                    'products'                        => 'Productos',
                    'products-hint-tooltip'           => 'Si se selecciona, esta ruta estará disponible para elegirla en el producto.',
                    'product-categories'              => 'Categorías de productos',
                    'product-categories-hint-tooltip' => 'Si se selecciona, esta ruta estará disponible para elegirla en la categoría de producto.',
                    'warehouses'                      => 'Almacenes',
                    'warehouses-hint-tooltip'         => 'Cuando se asigna un almacén a esta ruta, se considerará la ruta predeterminada para los productos que pasen por ese almacén.',
                    'packaging'                       => 'Empaque',
                    'packaging-hint-tooltip'          => 'Si se selecciona, esta ruta estará disponible para elegirla en el empaque.',
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
