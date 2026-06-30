<?php

return [
    'navigation' => [
        'title' => 'Cantidades',
        'group' => 'Ajustes',
    ],

    'form' => [
        'fields' => [
            'location'         => 'Ubicación',
            'product'          => 'Producto',
            'package'          => 'Paquete',
            'lot'              => 'Lote / Números de serie',
            'counted-qty'      => 'Cantidad contada',
            'scheduled-at'     => 'Programado el',
            'storage-category' => 'Categoría de almacenamiento',
        ],
    ],

    'table' => [
        'columns' => [
            'location'           => 'Ubicación',
            'product'            => 'Producto',
            'product-category'   => 'Categoría de producto',
            'lot'                => 'Lote / Números de serie',
            'storage-category'   => 'Categoría de almacenamiento',
            'available-quantity' => 'Cantidad disponible',
            'quantity'           => 'Cantidad',
            'package'            => 'Paquete',
            'last-counted-at'    => 'Último conteo el',
            'on-hand'            => 'Cantidad disponible',
            'uom'                => 'UOM',
            'counted'            => 'Cantidad contada',
            'difference'         => 'Diferencia',
            'scheduled-at'       => 'Programado el',
            'user'               => 'Usuario',
            'company'            => 'Empresa',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Cantidad actualizada',
                    'body'  => 'La cantidad ha sido actualizada correctamente.',
                ],
            ],
        ],

        'groups' => [
            'product'          => 'Producto',
            'product-category' => 'Categoría de producto',
            'location'         => 'Ubicación',
            'storage-category' => 'Categoría de almacenamiento',
            'lot'              => 'Lote / Números de serie',
            'company'          => 'Empresa',
            'package'          => 'Paquete',
        ],

        'filters' => [
            'product'             => 'Producto',
            'uom'                 => 'Unidad de medida',
            'product-category'    => 'Categoría de producto',
            'location'            => 'Ubicación',
            'storage-category'    => 'Categoría de almacenamiento',
            'lot'                 => 'Lote / Números de serie',
            'company'             => 'Empresa',
            'package'             => 'Paquete',
            'on-hand-quantity'    => 'Cantidad disponible',
            'difference-quantity' => 'Cantidad de diferencia',
            'incoming-at'         => 'Entrada el',
            'scheduled-at'        => 'Programado el',
            'user'                => 'Usuario',
            'created-at'          => 'Creado el',
            'updated-at'          => 'Actualizado el',
            'company'             => 'Empresa',
            'creator'             => 'Creador',
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Agregar cantidad',

                'notification' => [
                    'title' => 'Cantidad agregada',
                    'body'  => 'La cantidad ha sido agregada correctamente.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'La cantidad ya existe',
                        'body'  => 'Ya existe una cantidad para esta configuración. Por favor, actualice la cantidad existente.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'apply' => [
                'label' => 'Aplicar',

                'notification' => [
                    'title' => 'Cambios de cantidad aplicados',
                    'body'  => 'Los cambios de cantidad han sido aplicados correctamente.',
                ],
            ],

            'clear' => [
                'label' => 'Limpiar',

                'notification' => [
                    'title' => 'Cambios de cantidad eliminados',
                    'body'  => 'Los cambios de cantidad han sido eliminados correctamente.',
                ],
            ],
        ],
    ],
];
