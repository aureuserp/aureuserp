<?php

return [
    'navigation' => [
        'title' => 'Acuerdos de Compra',
        'group' => 'Compra',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'vendor'                => 'Proveedor',
                    'valid-from'            => 'Válido Desde',
                    'valid-to'              => 'Válido Hasta',
                    'buyer'                 => 'Comprador',
                    'reference'             => 'Referencia',
                    'reference-placeholder' => 'Ej. OC/123',
                    'agreement-type'        => 'Tipo de Acuerdo',
                    'company'               => 'Empresa',
                    'currency'              => 'Moneda',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Productos',

                'fields' => [
                    'product'    => 'Producto',
                    'quantity'   => 'Cantidad',
                    'ordered'    => 'Pedido',
                    'uom'        => 'Unidad de Medida',
                    'unit-price' => 'Precio Unitario',
                ],
            ],

            'additional' => [
                'title' => 'Información Adicional',
            ],

            'terms' => [
                'title' => 'Términos y Condiciones',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'agreement'      => 'Acuerdo',
            'vendor'         => 'Proveedor',
            'agreement-type' => 'Tipo de Acuerdo',
            'buyer'          => 'Comprador',
            'company'        => 'Empresa',
            'valid-from'     => 'Válido Desde',
            'valid-to'       => 'Válido Hasta',
            'reference'      => 'Referencia',
            'status'         => 'Estado',
        ],

        'groups' => [
            'agreement-type' => 'Tipo de Acuerdo',
            'vendor'         => 'Proveedor',
            'state'          => 'Estado',
            'created-at'     => 'Creado El',
            'updated-at'     => 'Actualizado El',
        ],

        'filters' => [
            'agreement'      => 'Acuerdo',
            'vendor'         => 'Proveedor',
            'agreement-type' => 'Tipo de Acuerdo',
            'buyer'          => 'Comprador',
            'company'        => 'Empresa',
            'valid-from'     => 'Válido Desde',
            'valid-to'       => 'Válido Hasta',
            'reference'      => 'Referencia',
            'status'         => 'Estado',
            'created-at'     => 'Creado El',
            'updated-at'     => 'Actualizado El',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Acuerdo de compra eliminado',
                    'body'  => 'El acuerdo de compra ha sido eliminado exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Acuerdo de compra restaurado',
                    'body'  => 'El acuerdo de compra ha sido restaurado exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Acuerdo de compra eliminado permanentemente',
                        'body'  => 'El acuerdo de compra ha sido eliminado permanentemente exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el acuerdo de compra',
                        'body'  => 'El acuerdo de compra no puede ser eliminado porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Acuerdos de compra eliminados',
                    'body'  => 'Los acuerdos de compra han sido eliminados exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Acuerdos de compra restaurados',
                    'body'  => 'Los acuerdos de compra han sido restaurados exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Acuerdos de compra eliminados permanentemente',
                        'body'  => 'Los acuerdos de compra han sido eliminados permanentemente exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los acuerdos de compra',
                        'body'  => 'Los acuerdos de compra no pueden ser eliminados porque están actualmente en uso.',
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
                    'valid-from'            => 'Válido Desde',
                    'valid-to'              => 'Válido Hasta',
                    'buyer'                 => 'Comprador',
                    'reference'             => 'Referencia',
                    'reference-placeholder' => 'Ej. OC/123',
                    'agreement-type'        => 'Tipo de Acuerdo',
                    'company'               => 'Empresa',
                    'currency'              => 'Moneda',
                ],
            ],

            'metadata' => [
                'title' => 'Metadatos',

                'entries' => [
                    'created-at' => 'Creado El',
                    'created-by' => 'Creado Por',
                    'updated-at' => 'Actualizado El',
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
                    'uom'        => 'Unidad de Medida',
                    'unit-price' => 'Precio Unitario',
                ],
            ],

            'additional' => [
                'title' => 'Información Adicional',
            ],

            'terms' => [
                'title' => 'Términos y Condiciones',
            ],
        ],
    ],
];
