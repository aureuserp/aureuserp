<?php

return [
    'title' => 'Productos de Plantilla de Pedido',

    'navigation' => [
        'title' => 'Productos de Plantilla de Pedido',
        'group' => 'Órdenes de Venta',
    ],

    'global-search' => [
        'name' => 'Nombre',
    ],

    'form' => [
        'fields' => [
            'sort'           => 'Orden',
            'order-template' => 'Plantilla de Pedido',
            'company'        => 'Empresa',
            'product'        => 'Producto',
            'product-uom'    => 'Unidad de Medida del Producto',
            'creator'        => 'Creador',
            'display-type'   => 'Tipo de Visualización',
            'name'           => 'Nombre',
            'quantity'       => 'Cantidad',
        ],
    ],

    'table' => [
        'columns' => [
            'sort'           => 'Orden',
            'order-template' => 'Plantilla de Pedido',
            'company'        => 'Empresa',
            'product'        => 'Producto',
            'product-uom'    => 'Unidad de Medida del Producto',
            'created-by'     => 'Creado Por',
            'display-type'   => 'Tipo de Visualización',
            'name'           => 'Nombre',
            'quantity'       => 'Cantidad',
            'created-at'     => 'Creado El',
            'updated-at'     => 'Actualizado El',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Productos de Plantilla de Pedido actualizados',
                    'body'  => 'Los productos de la plantilla de pedido han sido actualizados exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Productos de Plantilla de Pedido eliminados',
                    'body'  => 'Los productos de la plantilla de pedido han sido eliminados exitosamente.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Productos de Plantilla de Pedido eliminados',
                    'body'  => 'Los productos de la plantilla de pedido han sido eliminados exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'sort'           => 'Orden de Clasificación',
            'order-template' => 'Plantilla de Pedido',
            'company'        => 'Empresa',
            'product'        => 'Producto',
            'product-uom'    => 'Unidad de Medida del Producto',
            'display-type'   => 'Tipo de Visualización',
            'name'           => 'Nombre',
            'quantity'       => 'Cantidad',
        ],
    ],
];
