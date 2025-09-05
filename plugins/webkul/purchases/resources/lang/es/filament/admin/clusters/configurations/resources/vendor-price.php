<?php

return [
    'navigation' => [
        'title' => 'Listas de Precios de Proveedor',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'vendor'                      => 'Proveedor',
                    'vendor-product-name'         => 'Nombre de Producto del Proveedor',
                    'vendor-product-name-tooltip' => 'El nombre del producto del proveedor aparecerá en la solicitud de cotización. Deje este campo en blanco para usar el nombre de producto interno.',
                    'vendor-product-code'         => 'Código de Producto del Proveedor',
                    'vendor-product-code-tooltip' => 'El código de producto del proveedor aparecerá en la solicitud de cotización. Deje este campo en blanco para usar el código interno.',
                    'delay'                       => 'Plazo de Entrega (Días)',
                    'delay-tooltip'               => 'El plazo (en días) desde la confirmación de la orden de compra hasta la recepción del producto en el almacén. Utilizado por el programador para la planificación automática de órdenes de compra.',
                ],
            ],

            'prices' => [
                'title'  => 'Precios',

                'fields' => [
                    'product'            => 'Producto',
                    'quantity'           => 'Cantidad',
                    'quantity-tooltip'   => 'La cantidad mínima requerida para comprar a este proveedor y calificar para el precio especificado. Esto se expresa en la Unidad de Medida del Producto del proveedor o, si no se establece, la unidad de medida predeterminada del producto.',
                    'unit-price'         => 'Precio Unitario',
                    'unit-price-tooltip' => 'El precio por unidad para este producto del proveedor, expresado en la Unidad de Medida del Producto del proveedor o, si no se establece, la unidad de medida predeterminada del producto.',
                    'currency'           => 'Moneda',
                    'valid-from'         => 'Válido Desde',
                    'valid-to'           => 'Válido Hasta',
                    'discount'           => 'Descuento (%)',
                    'company'            => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'vendor'              => 'Proveedor',
            'vendor-product-name' => 'Nombre de Producto del Proveedor',
            'vendor-product-code' => 'Código de Producto del Proveedor',
            'delay'               => 'Plazo de Entrega (Días)',
            'product'             => 'Producto',
            'quantity'            => 'Cantidad',
            'unit-price'          => 'Precio Unitario',
            'currency'            => 'Moneda',
            'valid-from'          => 'Válido Desde',
            'valid-to'            => 'Válido Hasta',
            'discount'            => 'Descuento (%)',
            'company'             => 'Empresa',
            'created-at'          => 'Creado El',
            'updated-at'          => 'Actualizado El',
        ],

        'filters' => [
            'vendor'        => 'Filtrar por Proveedor',
            'product'       => 'Filtrar por Producto',
            'currency'      => 'Filtrar por Moneda',
            'company'       => 'Filtrar por Empresa',
            'price-from'    => 'Precio Mínimo',
            'price-to'      => 'Precio Máximo',
            'min-qty-from'  => 'Cantidad Mínima Desde',
            'min-qty-to'    => 'Cantidad Mínima Hasta',
            'starts-from'   => 'Fecha de Inicio de Validez',
            'ends-before'   => 'Fecha de Fin de Validez',
            'created-from'  => 'Creado Desde',
            'created-until' => 'Creado Hasta',
        ],

        'groups' => [
            'vendor'     => 'Proveedor',
            'product'    => 'Producto',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Precio de proveedor eliminado',
                        'body'  => 'El precio de proveedor ha sido eliminado exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el precio de proveedor',
                        'body'  => 'El precio de proveedor no puede ser eliminado porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Precios de proveedor eliminados',
                        'body'  => 'Los precios de proveedor han sido eliminados exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los precios de proveedor',
                        'body'  => 'Los precios de proveedor no pueden ser eliminados porque están actualmente en uso.',
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
                    'vendor-product-name'         => 'Nombre de Producto del Proveedor',
                    'vendor-product-name-tooltip' => 'El nombre del producto del proveedor aparecerá en la solicitud de cotización. Deje este campo en blanco para usar el nombre de producto interno.',
                    'vendor-product-code'         => 'Código de Producto del Proveedor',
                    'vendor-product-code-tooltip' => 'El código de producto del proveedor aparecerá en la solicitud de cotización. Deje este campo en blanco para usar el código interno.',
                    'delay'                       => 'Plazo de Entrega (Días)',
                    'delay-tooltip'               => 'El plazo (en días) desde la confirmación de la orden de compra hasta la recepción del producto en el almacén. Utilizado por el programador para la planificación automática de órdenes de compra.',
                ],
            ],

            'record-information' => [
                'title'  => 'Información del Registro',

                'entries' => [
                    'created-by'   => 'Creado Por',
                    'created-at'   => 'Creado El',
                    'last-updated' => 'Última Actualización',
                ],
            ],

            'prices' => [
                'title'  => 'Precios',

                'entries' => [
                    'product'            => 'Producto',
                    'quantity'           => 'Cantidad',
                    'quantity-tooltip'   => 'La cantidad mínima requerida para comprar a este proveedor y calificar para el precio especificado. Esto se expresa en la Unidad de Medida del Producto del proveedor o, si no se establece, la unidad de medida predeterminada del producto.',
                    'unit-price'         => 'Precio Unitario',
                    'unit-price-tooltip' => 'El precio por unidad para este producto del proveedor, expresado en la Unidad de Medida del Producto del proveedor o, si no se establece, la unidad de medida predeterminada del producto.',
                    'currency'           => 'Moneda',
                    'valid-from'         => 'Válido Desde',
                    'valid-to'           => 'Válido Hasta',
                    'discount'           => 'Descuento (%)',
                    'company'            => 'Empresa',
                ],
            ],
        ],
    ],
];
