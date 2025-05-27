<?php

return [
    'navigation' => [
        'title' => 'Rutas',
        'group' => 'Gestión de Almacenes',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'route'             => 'Ruta',
                    'route-placeholder' => 'ej. Recepción en Dos Pasos',
                    'company'           => 'Empresa',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Aplicable En',
                'description' => 'Elige las ubicaciones donde esta ruta puede ser aplicada.',

                'fields' => [
                    'products'                          => 'Productos',
                    'products-hint-tooltip'             => 'Si se selecciona, esta ruta estará disponible para su selección en el producto.',
                    'product-categories'                => 'Categorías de Producto',
                    'product-categories-hint-tooltip'   => 'Si se selecciona, esta ruta estará disponible para su selección en la categoría de producto.',
                    'warehouses'                        => 'Almacenes',
                    'warehouses-hint-tooltip'           => 'Cuando un almacén se asigna a esta ruta, se considerará la ruta predeterminada para los productos que se mueven a través de ese almacén.',
                    'packaging'                         => 'Embalaje',
                    'packaging-hint-tooltip'            => 'Si se selecciona, esta ruta estará disponible para su selección en el embalaje.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'route'      => 'Ruta',
            'company'    => 'Empresa',
            'deleted-at' => 'Eliminado El',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'groups' => [
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'filters' => [
            'company' => 'Empresa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Ruta actualizada',
                    'body'  => 'La ruta ha sido actualizada exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Ruta restaurada',
                    'body'  => 'La ruta ha sido restaurada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Ruta eliminada',
                    'body'  => 'La ruta ha sido eliminada exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ruta eliminada permanentemente',
                        'body'  => 'La ruta ha sido eliminada permanentemente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar la ruta',
                        'body'  => 'La ruta no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Rutas restauradas',
                    'body'  => 'Las rutas han sido restauradas exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Rutas eliminadas',
                    'body'  => 'Las rutas han sido eliminadas exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Rutas eliminadas permanentemente',
                        'body'  => 'Las rutas han sido eliminadas permanentemente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar las rutas',
                        'body'  => 'Las rutas no se pueden eliminar porque están actualmente en uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'General',
                'entries' => [
                    'route'             => 'Ruta',
                    'route-placeholder' => 'ej. Recepción en Dos Pasos',
                    'company'           => 'Empresa',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Aplicable En',
                'description' => 'Selecciona los lugares donde esta ruta puede ser seleccionada.',
                'entries' => [
                    'products'                          => 'Productos',
                    'products-hint-tooltip'             => 'Si se selecciona, esta ruta estará disponible para su selección en el producto.',
                    'product-categories'                => 'Categorías de Producto',
                    'product-categories-hint-tooltip'   => 'Si se selecciona, esta ruta estará disponible para su selección en la categoría de producto.',
                    'warehouses'                        => 'Almacenes',
                    'warehouses-hint-tooltip'           => 'Cuando un almacén se asigna a esta ruta, se considerará la ruta predeterminada para los productos que se mueven a través de ese almacén.',
                    'packaging'                         => 'Embalaje',
                    'packaging-hint-tooltip'            => 'Si se selecciona, esta ruta estará disponible para su selección en el embalaje.',
                ],
            ],

            'record-information' => [
                'title'   => 'Información del Registro',
                'entries' => [
                    'created-by'   => 'Creado Por',
                    'created-at'   => 'Creado En',
                    'last-updated' => 'Última Actualización',
                ],
            ],
        ],
    ],
];