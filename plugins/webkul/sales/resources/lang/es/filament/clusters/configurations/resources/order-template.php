<?php

return [
    'title' => 'Productos de plantilla de pedido',

    'navigation' => [
        'title' => 'Productos de plantilla de pedido',
        'group' => 'Pedidos de venta',
    ],

    'global-search' => [
        'name'    => 'Nombre',
    ],

    'form' => [
        'fields' => [
            'sort'           => 'Orden',
            'order-template' => 'Plantilla de pedido',
            'company'        => 'Empresa',
            'product'        => 'Producto',
            'product-uom'    => 'Unidad de medida del producto',
            'creator'        => 'Creador',
            'display-type'   => 'Tipo de visualización',
            'name'           => 'Nombre',
            'quantity'       => 'Cantidad',
        ],
    ],

    'table' => [
        'columns' => [
            'sort'           => 'Orden',
            'order-template' => 'Plantilla de pedido',
            'company'        => 'Empresa',
            'product'        => 'Producto',
            'product-uom'    => 'Unidad de medida del producto',
            'created-by'     => 'Creado por',
            'display-type'   => 'Tipo de visualización',
            'name'           => 'Nombre',
            'quantity'       => 'Cantidad',
            'created-at'     => 'Creado el',
            'updated-at'     => 'Actualizado el',

        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Productos de plantilla de pedido actualizados',
                    'body'  => 'Los productos de plantilla de pedido se han actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Productos de plantilla de pedido eliminados',
                    'body'  => 'Los productos de plantilla de pedido se han eliminado correctamente.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Productos de plantilla de pedido eliminados',
                    'body'  => 'Los productos de plantilla de pedido se han eliminado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'sort'           => 'Orden de clasificación',
            'order-template' => 'Plantilla de pedido',
            'company'        => 'Empresa',
            'product'        => 'Producto',
            'product-uom'    => 'Unidad de medida del producto',
            'display-type'   => 'Tipo de visualización',
            'name'           => 'Nombre',
            'quantity'       => 'Cantidad',
        ],
    ],
];
