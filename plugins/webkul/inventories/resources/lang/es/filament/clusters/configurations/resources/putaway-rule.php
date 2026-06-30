<?php

return [
    'navigation' => [
        'title' => 'Reglas de almacenamiento',
        'group' => 'Gestión de almacenes',
    ],

    'form' => [
        'fields' => [
            'in-location'          => 'Cuando el producto llega a',
            'product'              => 'Producto',
            'product-placeholder'  => 'Todos los productos',
            'category'             => 'Categoría de producto',
            'category-placeholder' => 'Todas las categorías',
            'storage-category'     => 'Categoría de almacenamiento',
            'out-location'         => 'Almacenar en',
            'sub-location'         => 'Sububicación',
            'company'              => 'Empresa',
        ],
    ],

    'table' => [
        'columns' => [
            'in-location'      => 'Cuando el producto llega a',
            'product'          => 'Producto',
            'category'         => 'Categoría de producto',
            'storage-category' => 'Categoría de almacenamiento',
            'out-location'     => 'Almacenar en',
            'sub-location'     => 'Sububicación',
            'company'          => 'Empresa',
            'deleted-at'       => 'Eliminado el',
            'created-at'       => 'Creado el',
            'updated-at'       => 'Actualizado el',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Regla de almacenamiento actualizada',
                    'body'  => 'La regla de almacenamiento se ha actualizado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Regla de almacenamiento restaurada',
                    'body'  => 'La regla de almacenamiento se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Regla de almacenamiento eliminada',
                    'body'  => 'La regla de almacenamiento se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'No se pudo eliminar la regla de almacenamiento',
                        'body'  => 'La regla de almacenamiento no se puede eliminar permanentemente porque está referenciada por otros registros.',
                    ],

                    'success' => [
                        'title' => 'Regla de almacenamiento eliminada permanentemente',
                        'body'  => 'La regla de almacenamiento se ha eliminado permanentemente correctamente.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Reglas de almacenamiento restauradas',
                    'body'  => 'Las reglas de almacenamiento se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Reglas de almacenamiento eliminadas',
                    'body'  => 'Las reglas de almacenamiento se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'No se pudieron eliminar las reglas de almacenamiento',
                        'body'  => 'Algunas reglas de almacenamiento no se pueden eliminar permanentemente porque están referenciadas por otros registros.',
                    ],

                    'success' => [
                        'title' => 'Reglas de almacenamiento eliminadas permanentemente',
                        'body'  => 'Las reglas de almacenamiento se han eliminado permanentemente correctamente.',
                    ],
                ],
            ],
        ],
    ],
];
