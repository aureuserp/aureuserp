<?php

return [
    'navigation' => [
        'title' => 'Cantidades',
        'group' => 'Ajustes',
    ],

    'form' => [
        'fields' => [
            'location'          => 'Ubicación',
            'product'           => 'Producto',
            'package'           => 'Paquete',
            'lot'               => 'Lote / Números de Serie',
            'counted-qty'       => 'Cantidad Contada',
            'scheduled-at'      => 'Programado Para',
            'storage-category'  => 'Categoría de Almacenamiento',
        ],
    ],

    'table' => [
        'columns' => [
            'location'                  => 'Ubicación',
            'product'                   => 'Producto',
            'product-category'          => 'Categoría de Producto',
            'lot'                       => 'Lote / Números de Serie',
            'storage-category'          => 'Categoría de Almacenamiento',
            'available-quantity'        => 'Cantidad Disponible',
            'quantity'                  => 'Cantidad',
            'package'                   => 'Paquete',
            'last-counted-at'           => 'Última Vez Contado En',
            'on-hand'                   => 'Cantidad En Existencia',
            'counted'                   => 'Cantidad Contada',
            'difference'                => 'Diferencia',
            'scheduled-at'              => 'Programado Para',
            'user'                      => 'Usuario',
            'company'                   => 'Empresa',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Cantidad actualizada',
                    'body'  => 'La cantidad ha sido actualizada exitosamente.',
                ],
            ],
        ],

        'groups' => [
            'product'           => 'Producto',
            'product-category'  => 'Categoría de Producto',
            'location'          => 'Ubicación',
            'storage-category'  => 'Categoría de Almacenamiento',
            'lot'               => 'Lote / Números de Serie',
            'company'           => 'Empresa',
            'package'           => 'Paquete',
        ],

        'filters' => [
            'product'               => 'Producto',
            'uom'                   => 'Unidad de Medida',
            'product-category'      => 'Categoría de Producto',
            'location'              => 'Ubicación',
            'storage-category'      => 'Categoría de Almacenamiento',
            'lot'                   => 'Lote / Números de Serie',
            'company'               => 'Empresa',
            'package'               => 'Paquete',
            'on-hand-quantity'      => 'Cantidad En Existencia',
            'difference-quantity'   => 'Cantidad de Diferencia',
            'incoming-at'           => 'Entrando El',
            'scheduled-at'          => 'Programado Para',
            'user'                  => 'Usuario',
            'created-at'            => 'Creado El',
            'updated-at'            => 'Actualizado El',
            'company'               => 'Empresa',
            'creator'               => 'Creador',
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Agregar Cantidad',

                'notification' => [
                    'title' => 'Cantidad agregada',
                    'body'  => 'La cantidad ha sido agregada exitosamente.',
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
                    'body'  => 'Los cambios de cantidad han sido aplicados exitosamente.',
                ],
            ],

            'clear' => [
                'label' => 'Limpiar',

                'notification' => [
                    'title' => 'Cambios de cantidad limpiados',
                    'body'  => 'Los cambios de cantidad han sido limpiados exitosamente.',
                ],
            ],
        ],
    ],
];
