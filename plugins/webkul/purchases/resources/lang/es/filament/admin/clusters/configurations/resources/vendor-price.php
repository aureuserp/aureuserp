<?php

return [
    'navigation' => [
        'title' => 'Listas de precios de proveedor',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'vendor'                      => 'Proveedor',
                    'vendor-product-name'         => 'Nombre del producto del proveedor',
                    'vendor-product-name-tooltip' => 'El nombre del producto del proveedor aparecerá en la solicitud de presupuesto. Dejar en blanco para usar el nombre interno del producto.',
                    'vendor-product-code'         => 'Código del producto del proveedor',
                    'vendor-product-code-tooltip' => 'El código del producto del proveedor aparecerá en la solicitud de presupuesto. Dejar en blanco para usar el código interno.',
                    'delay'                       => 'Plazo de entrega (días)',
                    'delay-tooltip'               => 'El plazo de entrega (en días) desde la confirmación del pedido de compra hasta la recepción del producto en el almacén. Lo utiliza el planificador para la planificación automática de pedidos de compra.',
                ],
            ],

            'prices' => [
                'title'  => 'Precios',

                'fields' => [
                    'product'            => 'Producto',
                    'quantity'           => 'Cantidad',
                    'quantity-tooltip'   => 'La cantidad mínima requerida para comprar a este proveedor y poder optar al precio especificado. Se expresa en la unidad de medida del producto del proveedor o, si no se ha definido, en la unidad de medida predeterminada del producto.',
                    'unit-price'         => 'Precio unitario',
                    'unit-price-tooltip' => 'El precio por unidad de este producto del proveedor, expresado en la unidad de medida del producto del proveedor o, si no se ha definido, en la unidad de medida predeterminada del producto.',
                    'currency'           => 'Moneda',
                    'valid-from'         => 'Válido desde',
                    'valid-to'           => 'Válido hasta',
                    'discount'           => 'Descuento (%)',
                    'company'            => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'vendor'              => 'Proveedor',
            'vendor-product-name' => 'Nombre del producto del proveedor',
            'vendor-product-code' => 'Código del producto del proveedor',
            'delay'               => 'Plazo de entrega (días)',
            'product'             => 'Producto',
            'quantity'            => 'Cantidad',
            'unit-price'          => 'Precio unitario',
            'currency'            => 'Moneda',
            'valid-from'          => 'Válido desde',
            'valid-to'            => 'Válido hasta',
            'discount'            => 'Descuento (%)',
            'company'             => 'Empresa',
            'created-at'          => 'Creado el',
            'updated-at'          => 'Actualizado el',
        ],

        'filters' => [
            'vendor'        => 'Filtrar por proveedor',
            'product'       => 'Filtrar por producto',
            'currency'      => 'Filtrar por moneda',
            'company'       => 'Filtrar por empresa',
            'price-from'    => 'Precio mínimo',
            'price-to'      => 'Precio máximo',
            'min-qty-from'  => 'Cantidad mínima desde',
            'min-qty-to'    => 'Cantidad mínima hasta',
            'starts-from'   => 'Fecha de validez desde',
            'ends-before'   => 'Fecha de validez hasta',
            'created-from'  => 'Creado desde',
            'created-until' => 'Creado hasta',
        ],

        'groups' => [
            'vendor'     => 'Proveedor',
            'product'    => 'Producto',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Precio de proveedor eliminado',
                        'body'  => 'El precio de proveedor se ha eliminado correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el precio de proveedor',
                        'body'  => 'El precio de proveedor no se puede eliminar porque está en uso actualmente.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Precios de proveedor eliminados',
                        'body'  => 'Los precios de proveedor se han eliminado correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los precios de proveedor',
                        'body'  => 'Los precios de proveedor no se pueden eliminar porque están en uso actualmente.',
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
                    'vendor'                      => 'Proveedor',
                    'vendor-product-name'         => 'Nombre del producto del proveedor',
                    'vendor-product-name-tooltip' => 'El nombre del producto del proveedor aparecerá en la solicitud de presupuesto. Dejar en blanco para usar el nombre interno del producto.',
                    'vendor-product-code'         => 'Código del producto del proveedor',
                    'vendor-product-code-tooltip' => 'El código del producto del proveedor aparecerá en la solicitud de presupuesto. Dejar en blanco para usar el código interno.',
                    'delay'                       => 'Plazo de entrega (días)',
                    'delay-tooltip'               => 'El plazo de entrega (en días) desde la confirmación del pedido de compra hasta la recepción del producto en el almacén. Lo utiliza el planificador para la planificación automática de pedidos de compra.',
                ],
            ],

            'record-information' => [
                'title'  => 'Información del registro',

                'entries' => [
                    'created-by'   => 'Creado por',
                    'created-at'   => 'Creado el',
                    'last-updated' => 'Última actualización',
                ],
            ],

            'prices' => [
                'title'  => 'Precios',

                'entries' => [
                    'product'            => 'Producto',
                    'quantity'           => 'Cantidad',
                    'quantity-tooltip'   => 'La cantidad mínima requerida para comprar a este proveedor y poder optar al precio especificado. Se expresa en la unidad de medida del producto del proveedor o, si no se ha definido, en la unidad de medida predeterminada del producto.',
                    'unit-price'         => 'Precio unitario',
                    'unit-price-tooltip' => 'El precio por unidad de este producto del proveedor, expresado en la unidad de medida del producto del proveedor o, si no se ha definido, en la unidad de medida predeterminada del producto.',
                    'currency'           => 'Moneda',
                    'valid-from'         => 'Válido desde',
                    'valid-to'           => 'Válido hasta',
                    'discount'           => 'Descuento (%)',
                    'company'            => 'Empresa',
                ],
            ],
        ],
    ],
];
