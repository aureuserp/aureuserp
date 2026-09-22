<?php

return [
    'navigation' => [
        'title' => 'Listas de precios',
        'group' => 'Productos',
    ],

    'header-actions' => [
        'create' => [
            'label' => 'Nueva lista de precios',
        ],
    ],

    'form' => [
        'section' => [
            'general' => [
                'title' => 'Información general',

                'fields' => [
                    'name'     => 'Nombre de la lista de precios',
                    'currency' => 'Moneda',
                    'company'  => 'Empresa',
                    'status'   => 'Activo',
                ],
            ],

            'rules' => [
                'title'        => 'Reglas de precio',
                'description'  => 'La primera regla que coincide con un producto decide su precio. Las reglas más específicas prevalecen sobre las más generales.',
                'add-rule'     => 'Añadir regla',
                'all-products' => 'Todos los productos',

                'actions' => [
                    'edit' => 'Editar regla',
                ],

                'columns' => [
                    'apply-to'     => 'Aplicar en',
                    'target'       => 'Se aplica a',
                    'min-quantity' => 'Cant. mínima',
                    'type'         => 'Calcular precio',
                    'price'        => 'Precio',
                    'period'       => 'Periodo',
                ],

                'formula' => [
                    'discount'     => 'descuento',
                    'markup'       => 'margen',
                    'rule-tip'     => ':base con un :discount % de :type y :surcharge de tarifa adicional'."\n".'Ejemplo: :amount * :factor + :surcharge → :total',
                    'rounding-tip' => 'Consejo: ¿quiere redondear a 9,99? Redondee a 10,00 y establezca una tarifa adicional de -0,01.',
                ],

                'fields' => [
                    'apply-to-product'      => 'Producto',
                    'apply-to-category'     => 'Categoría',
                    'all-products'          => 'Todos los productos',
                    'all-categories'        => 'Todas las categorías',
                    'all-variants'          => 'Todas las variantes',
                    'variant'               => 'Variante',
                    'on'                    => 'sobre',
                    'sales-price'           => 'Precio de venta',
                    'apply-to'              => 'Aplicar a',
                    'product'               => 'Producto',
                    'category'              => 'Categoría de producto',
                    'min-quantity'          => 'Cant. mín.',
                    'type'                  => 'Tipo de precio',
                    'fixed-price'           => 'Precio fijo',
                    'percent-price'         => 'Descuento',
                    'percent-price-helper'  => 'Use un valor negativo para aplicar un margen.',
                    'base'                  => 'Precio base',
                    'base-price-list'       => 'Otra lista de precios',
                    'price-discount'        => 'Descuento',
                    'price-discount-helper' => 'Use un valor negativo para aplicar un margen.',
                    'price-markup'          => 'Margen',
                    'price-round'           => 'Redondear a',
                    'price-round-helper'    => 'Redondea el precio a un múltiplo de este valor, después del descuento y antes de la tarifa adicional.',
                    'price-surcharge'       => 'Tarifa adicional',
                    'price-min-margin'      => 'Margen mín.',
                    'price-max-margin'      => 'Margen máx.',
                    'starts-at'             => 'Periodo de validez',
                    'ends-at'               => 'Fecha de fin',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'     => 'Lista de precios',
            'currency' => 'Moneda',
            'company'  => 'Empresa',
            'rules'    => 'Reglas',
            'status'   => 'Activo',
        ],

        'filters' => [
            'status'   => 'Activo',
            'currency' => 'Moneda',
        ],
    ],
];
