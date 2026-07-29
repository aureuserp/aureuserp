<?php

return [
    'navigation' => [
        'title' => 'Acuerdos de compra',
        'group' => 'Compras',
    ],

    'global-search' => [
        'vendor' => 'Proveedor',
        'type'   => 'Tipo',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'vendor'                => 'Proveedor',
                    'valid-from'            => 'Válido desde',
                    'valid-to'              => 'Válido hasta',
                    'buyer'                 => 'Comprador',
                    'reference'             => 'Referencia',
                    'reference-placeholder' => 'p. ej. PO/123',
                    'agreement-type'        => 'Tipo de acuerdo',
                    'company'               => 'Empresa',
                    'currency'              => 'Moneda',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Productos',

                'columns' => [
                    'product'    => 'Producto',
                    'quantity'   => 'Cantidad',
                    'ordered'    => 'Pedido',
                    'uom'        => 'Unidad de medida',
                    'unit-price' => 'Precio unitario',
                ],

                'fields' => [
                    'product'    => 'Producto',
                    'quantity'   => 'Cantidad',
                    'ordered'    => 'Pedido',
                    'uom'        => 'Unidad de medida',
                    'unit-price' => 'Precio unitario',
                ],
            ],

            'additional' => [
                'title' => 'Información adicional',
            ],

            'terms' => [
                'title' => 'Términos y condiciones',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'agreement'      => 'Acuerdo',
            'vendor'         => 'Proveedor',
            'agreement-type' => 'Tipo de acuerdo',
            'buyer'          => 'Comprador',
            'company'        => 'Empresa',
            'valid-from'     => 'Válido desde',
            'valid-to'       => 'Válido hasta',
            'reference'      => 'Referencia',
            'status'         => 'Estado',
        ],

        'groups' => [
            'agreement-type' => 'Tipo de acuerdo',
            'vendor'         => 'Proveedor',
            'state'          => 'Estado',
            'created-at'     => 'Creado el',
            'updated-at'     => 'Actualizado el',
        ],

        'filters' => [
            'agreement'      => 'Acuerdo',
            'vendor'         => 'Proveedor',
            'agreement-type' => 'Tipo de acuerdo',
            'buyer'          => 'Comprador',
            'company'        => 'Empresa',
            'valid-from'     => 'Válido desde',
            'valid-to'       => 'Válido hasta',
            'reference'      => 'Referencia',
            'status'         => 'Estado',
            'created-at'     => 'Creado el',
            'updated-at'     => 'Actualizado el',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Acuerdo de compra eliminado',
                    'body'  => 'El acuerdo de compra se ha eliminado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Acuerdo de compra restaurado',
                    'body'  => 'El acuerdo de compra se ha restaurado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Acuerdo de compra eliminado permanentemente',
                        'body'  => 'El acuerdo de compra se ha eliminado permanentemente de forma correcta.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el acuerdo de compra',
                        'body'  => 'El acuerdo de compra no se puede eliminar porque está actualmente en uso.',
                    ],

                    'warning' => [
                        'title' => 'No se puede eliminar el acuerdo de compra',
                        'body'  => 'Solo se pueden eliminar los acuerdos de compra en estado Borrador o Cancelado.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Acuerdos de compra eliminados',
                    'body'  => 'Los acuerdos de compra se han eliminado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Acuerdos de compra restaurados',
                    'body'  => 'Los acuerdos de compra se han restaurado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Acuerdos de compra eliminados permanentemente',
                        'body'  => 'Los acuerdos de compra se han eliminado permanentemente de forma correcta.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los acuerdos de compra',
                        'body'  => 'Los acuerdos de compra no se pueden eliminar porque están actualmente en uso.',
                    ],

                    'warning' => [
                        'title' => 'No se puede eliminar el acuerdo de compra',
                        'body'  => 'Solo se pueden eliminar los acuerdos de compra en estado Borrador o Cancelado.',
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
                    'vendor'                => 'Proveedor',
                    'valid-from'            => 'Válido desde',
                    'valid-to'              => 'Válido hasta',
                    'buyer'                 => 'Comprador',
                    'reference'             => 'Referencia',
                    'reference-placeholder' => 'p. ej. PO/123',
                    'agreement-type'        => 'Tipo de acuerdo',
                    'company'               => 'Empresa',
                    'currency'              => 'Moneda',
                ],
            ],

            'metadata' => [
                'title' => 'Metadatos',

                'entries' => [
                    'created-at' => 'Creado el',
                    'created-by' => 'Creado por',
                    'updated-at' => 'Actualizado el',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Productos',

                'entries' => [
                    'product'    => 'Producto',
                    'quantity'   => 'Cantidad',
                    'ordered'    => 'Pedido',
                    'uom'        => 'Unidad de medida',
                    'unit-price' => 'Precio unitario',
                ],
            ],

            'additional' => [
                'title' => 'Información adicional',
            ],

            'terms' => [
                'title' => 'Términos y condiciones',
            ],
        ],
    ],
];
