<?php

return [
    'navigation' => [
        'title' => 'Punto de venta',
        'group' => 'Punto de venta',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'             => 'Punto de venta',
                    'name-placeholder' => 'p. ej. Tienda NYC',
                    'code'             => 'Código corto',
                    'code-helper-text' => 'Se usa como prefijo de los números de pedido y de sesión.',
                    'is-active'        => 'Activo',
                ],
            ],

            'configurations' => [
                'title' => 'Configuraciones',

                'tabs' => [
                    'restaurant' => [
                        'title' => 'Modo restaurante',

                        'fields' => [
                            'is-restaurant'                 => 'Es un bar/restaurante',
                            'is-restaurant-helper-text'     => 'Activa la gestión de mesas, la división de cuentas y los tickets de preparación.',
                            'enable-split-bill'             => 'Permitir dividir la cuenta',
                            'enable-split-bill-helper-text' => 'Dividir el total o las líneas del pedido.',
                            'enable-print-bill'             => 'Impresión anticipada del recibo',
                            'enable-print-bill-helper-text' => 'Permitir imprimir el recibo antes del pago.',
                            'enable-takeaway'               => 'Para comer aquí / Para llevar',
                            'enable-takeaway-helper-text'   => 'Ajusta el impuesto según si el cliente come en el local o pide para llevar.',
                            'takeaway-fiscal-position'      => 'Posición fiscal alternativa',
                            'floors'                        => 'Mapa de plantas y mesas',
                            'floors-helper-text'            => 'Diseña las plantas y asigna pedidos a las mesas.',
                        ],
                    ],

                    'payment' => [
                        'title' => 'Pago',

                        'fields' => [
                            'payment-methods'                        => 'Métodos de pago',
                            'enable-maximum-difference'              => 'Fijar diferencia máxima',
                            'enable-maximum-difference-helper-text'  => 'Fija una diferencia máxima permitida entre el dinero esperado y el contado durante el cierre de la sesión.',
                            'amount-authorized-diff'                 => 'Diferencia máxima',
                            'enable-cash-rounding'                   => 'Redondeo de efectivo',
                            'enable-cash-rounding-helper-text'       => 'Define la moneda más pequeña de la divisa usada para pagar en efectivo.',
                            'cash-rounding'                          => 'Método de redondeo',
                            'enable-only-round-cash-method'          => 'Aplicar el redondeo solo al efectivo',
                            'enable-tip'                             => 'Propinas',
                            'enable-tip-helper-text'                 => 'Acepta propinas del cliente o convierte su cambio en propina.',
                            'tip-product'                            => 'Producto de propina',
                        ],
                    ],

                    'interface' => [
                        'title' => 'Interfaz del TPV',

                        'fields' => [
                            'enable-customer-required'              => 'Cliente obligatorio',
                            'show-product-images'                   => 'Mostrar imágenes de productos',
                            'show-category-images'                  => 'Mostrar imágenes de categorías',
                            'limited-products-amount'               => 'Productos cargados',
                            'limited-products-amount-helper-text'   => 'Número de productos cargados en el terminal al abrir una sesión.',
                        ],
                    ],

                    'products' => [
                        'title' => 'Productos y categorías del TPV',

                        'fields' => [
                            'show-margins-and-costs'             => 'Mostrar márgenes y costes',
                            'show-margins-and-costs-helper-text' => 'Mostrar márgenes y costes en la información del producto.',
                            'limit-categories'                   => 'Restringir categorías',
                            'limit-categories-helper-text'       => 'Elige qué categorías de productos del TPV están disponibles.',
                            'locked-by-session'                  => 'Bloqueado mientras haya una sesión abierta',
                            'categories'                         => 'Categorías de productos del TPV disponibles',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Contabilidad',

                        'fields' => [
                            'journal'                                  => 'Diario de pedidos',
                            'journal-helper-text'                      => 'Diario usado para el asiento de cierre de la sesión.',
                            'invoice-journal'                          => 'Diario de facturas',
                            'is-closing-entry-by-product'              => 'Asiento de cierre por producto',
                            'is-closing-entry-by-product-helper-text'  => 'Muestra el desglose de las líneas de venta por producto en el asiento de cierre generado automáticamente.',
                            'enable-fiscal-position'                   => 'Impuestos flexibles',
                            'enable-fiscal-position-helper-text'       => 'Usa posiciones fiscales para aplicar impuestos distintos por pedido.',
                            'fiscal-position'                          => 'Posición fiscal predeterminada',
                            'fiscal-positions'                         => 'Posiciones fiscales',
                            'receivable-account'                       => 'Cuenta intermedia',
                            'receivable-account-helper-text'           => 'Déjalo vacío para usar la cuenta a cobrar predeterminada de la configuración del Punto de venta.',
                            'cash-movement-account'                    => 'Cuenta de entrada/salida de efectivo',
                            'balancing-account'                        => 'Cuenta de ajuste',
                            'enable-cogs'                              => 'Coste de las ventas',
                            'enable-cogs-helper-text'                  => 'Contabilizar el coste de los productos vendidos al cerrar la sesión.',
                            'cogs-journal'                             => 'Diario del coste de las ventas',
                            'stock-output-account'                     => 'Cuenta de salida de stock',
                        ],
                    ],

                    'pricing' => [
                        'title' => 'Precios',

                        'fields' => [
                            'tax-display'                            => 'Precios de producto',
                            'tax-display-helper-text'                => 'Precios de producto mostrados en el terminal y en los recibos.',
                            'enable-price-control'                   => 'Control de precios',
                            'enable-price-control-helper-text'       => 'Restringir la modificación de precios a los responsables.',
                            'enable-price-list'                      => 'Listas de precios flexibles',
                            'enable-price-list-helper-text'          => 'Define varios precios por producto, descuentos automáticos, etc.',
                            'price-list'                             => 'Lista de precios predeterminada',
                            'price-list-helper-text'                 => 'Se aplica a todos los pedidos de esta caja. Los cajeros solo pueden cambiar de lista si las listas de precios flexibles están activadas.',
                            'price-lists'                            => 'Listas de precios disponibles',
                            'enable-line-discount'                   => 'Descuentos por línea',
                            'enable-line-discount-helper-text'       => 'Permitir a los cajeros aplicar un descuento por línea.',
                            'enable-global-discount'                 => 'Descuentos globales',
                            'enable-global-discount-helper-text'     => 'Añade un botón para aplicar un descuento global.',
                            'discount-product'                       => 'Producto de descuento',
                            'global-discount-percentage'             => 'Descuento %',
                            'global-discount-percentage-helper-text' => 'Porcentaje predeterminado al aplicar un descuento global.',
                        ],
                    ],

                    'receipts' => [
                        'title' => 'Cuentas y recibos',

                        'fields' => [
                            'enable-receipt-print'                   => 'Impresión de recibos',
                            'enable-receipt-print-helper-text'       => 'Muestra un botón Imprimir en la pantalla del recibo tras el pago.',
                            'enable-receipt-auto-print'              => 'Impresión automática de recibos',
                            'enable-receipt-auto-print-helper-text'  => 'Imprime los recibos automáticamente en cuanto se registra el pago.',
                            'receipt-header'                         => 'Encabezado del recibo',
                            'receipt-footer'                         => 'Pie del recibo',
                            'bills'                                  => 'Monedas/Billetes',
                            'bills-helper-text'                      => 'Denominaciones ofrecidas al contar el efectivo en la apertura y el cierre.',
                        ],
                    ],

                    'preparation' => [
                        'title' => 'Preparación',

                        'fields' => [
                            'printers'             => 'Impresoras de preparación',
                            'printers-helper-text' => 'Imprime los pedidos en la cocina, en la barra, etc.',
                        ],
                    ],

                    'inventory' => [
                        'title' => 'Inventario',

                        'fields' => [
                            'operation-type'                => 'Tipo de operación',
                            'operation-type-helper-text'    => 'Se usa para registrar los albaranes de productos. Los productos se consumen de su ubicación de origen predeterminada.',
                            'return-operation-type'         => 'Tipo de operación de devolución',
                            'warehouse'                     => 'Almacén',
                            'enable-ship-later'             => 'Permitir envío posterior',
                            'enable-ship-later-helper-text' => 'Vende productos y entrégalos más tarde.',
                            'ship-later-route'              => 'Ruta específica',
                            'picking-policy'                => 'Política de envío',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'name'           => 'Punto de venta',
                    'code'           => 'Código corto',
                    'company-name'   => 'Empresa',
                    'warehouse-name' => 'Almacén',
                    'journal-name'   => 'Diario de pedidos',
                    'is-restaurant'  => 'Es un bar/restaurante',
                    'is-active'      => 'Activo',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Nombre',
            'closing'        => 'Cierre',
            'balance'        => 'Saldo',
            'code'           => 'Código',
            'warehouse'      => 'Almacén',
            'operation-type' => 'Tipo de operación',
            'journal'        => 'Diario de ventas',
            'is-restaurant'  => 'Restaurante',
            'is-active'      => 'Activo',
            'company'        => 'Empresa',
        ],

        'groups' => [
            'warehouse' => 'Almacén',
            'company'   => 'Empresa',
        ],

        'record-actions' => [
            'restore' => [
                'notification' => [
                    'success' => [
                        'title' => 'Punto de venta restaurado',
                        'body'  => 'El punto de venta se ha restaurado.',
                    ],
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Punto de venta eliminado',
                        'body'  => 'El punto de venta se ha eliminado.',
                    ],
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Punto de venta eliminado permanentemente',
                        'body'  => 'El punto de venta se ha eliminado permanentemente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el punto de venta',
                        'body'  => 'El punto de venta no se puede eliminar permanentemente porque tiene sesiones o pedidos. Archívelo en su lugar.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Puntos de venta eliminados permanentemente',
                        'body'  => 'Los puntos de venta seleccionados se han eliminado permanentemente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los puntos de venta',
                        'body'  => 'Uno o más puntos de venta no se pueden eliminar permanentemente porque tienen sesiones o pedidos. Archívelos en su lugar.',
                    ],
                ],
            ],
        ],
    ],
];
