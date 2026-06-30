<?php

return [
    'navigation' => [
        'title' => 'Productos',
        'group' => 'Inventario',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'             => 'Nombre',
                    'name-placeholder' => 'ej. Camiseta',
                    'description'      => 'Descripción',
                    'tags'             => 'Etiquetas',
                    'sales'            => 'Ventas',
                    'purchase'         => 'Compra',
                ],
            ],

            'invoice-policy' => [
                'title'            => 'Política de facturación',
                'ordered-policy'   => 'Puede facturar los bienes antes de que se entreguen.',
                'delivered-policy' => 'Facturar después de la entrega, según las cantidades entregadas, no las pedidas.',
            ],

            'images' => [
                'title' => 'Imágenes',
            ],

            'settings' => [
                'title' => 'Configuración',

                'fields' => [
                    'type'      => 'Tipo',
                    'reference' => 'Referencia',
                    'barcode'   => 'Código de barras',
                    'category'  => 'Categoría',
                    'company'   => 'Empresa',
                ],
            ],

            'category-and-tags' => [
                'title' => 'Categoría y etiquetas',

                'fields' => [
                    'category' => 'Categoría',
                    'tags'     => 'Etiquetas',
                ],
            ],

            'pricing' => [
                'title' => 'Precios',

                'fields' => [
                    'price' => 'Precio',
                    'cost'  => 'Coste',
                ],
            ],

            'additional' => [
                'title' => 'Adicional',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Nombre',
            'images'      => 'Imágenes',
            'type'        => 'Tipo',
            'reference'   => 'Referencia',
            'responsible' => 'Responsable',
            'barcode'     => 'Código de barras',
            'category'    => 'Categoría',
            'company'     => 'Empresa',
            'price'       => 'Precio',
            'cost'        => 'Coste',
            'tags'        => 'Etiquetas',
            'deleted-at'  => 'Eliminado el',
            'created-at'  => 'Creado el',
            'updated-at'  => 'Actualizado el',
        ],

        'groups' => [
            'type'       => 'Tipo',
            'category'   => 'Categoría',
            'created-at' => 'Creado el',
        ],

        'filters' => [
            'name'        => 'Nombre',
            'type'        => 'Tipo',
            'reference'   => 'Referencia',
            'barcode'     => 'Código de barras',
            'category'    => 'Categoría',
            'company'     => 'Empresa',
            'price'       => 'Precio',
            'cost'        => 'Coste',
            'is-favorite' => 'Es favorito',
            'weight'      => 'Peso',
            'volume'      => 'Volumen',
            'tags'        => 'Etiquetas',
            'responsible' => 'Responsable',
            'created-at'  => 'Creado el',
            'updated-at'  => 'Actualizado el',
            'creator'     => 'Creador',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Producto restaurado',
                    'body'  => 'El producto se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Producto eliminado',
                    'body'  => 'El producto se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Producto eliminado permanentemente',
                    'body'  => 'El producto se ha eliminado permanentemente correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Productos restaurados',
                    'body'  => 'Los productos se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Productos eliminados',
                    'body'  => 'Los productos se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Productos eliminados permanentemente',
                    'body'  => 'Los productos se han eliminado permanentemente correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'name'             => 'Nombre',
                    'name-placeholder' => 'ej. Camiseta',
                    'description'      => 'Descripción',
                    'tags'             => 'Etiquetas',
                ],
            ],

            'images' => [
                'title' => 'Imágenes',

                'entries' => [],
            ],

            'settings' => [
                'title' => 'Configuración',

                'entries' => [
                    'type'      => 'Tipo',
                    'reference' => 'Referencia',
                    'barcode'   => 'Código de barras',
                    'category'  => 'Categoría',
                    'company'   => 'Empresa',
                ],
            ],

            'pricing' => [
                'title' => 'Precios',

                'entries' => [
                    'price' => 'Precio',
                    'cost'  => 'Coste',
                ],
            ],

            'inventory' => [
                'title' => 'Inventario',

                'entries' => [],

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Seguimiento',

                        'entries' => [
                            'track-inventory' => 'Seguimiento de inventario',
                            'track-by'        => 'Seguimiento por',
                            'expiration-date' => 'Fecha de caducidad',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Operaciones',

                        'entries' => [
                            'routes' => 'Rutas',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Logística',

                        'entries' => [
                            'responsible' => 'Responsable',
                            'weight'      => 'Peso',
                            'volume'      => 'Volumen',
                            'sale-delay'  => 'Plazo de entrega al cliente (días)',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Trazabilidad',

                        'entries' => [
                            'expiration-date'  => 'Fecha de caducidad (días)',
                            'best-before-date' => 'Fecha de consumo preferente (días)',
                            'removal-date'     => 'Fecha de retirada (días)',
                            'alert-date'       => 'Fecha de alerta (días)',
                        ],
                    ],
                ],
            ],

            'record-information' => [
                'title' => 'Información del registro',

                'entries' => [
                    'created-at' => 'Creado el',
                    'created-by' => 'Creado por',
                    'updated-at' => 'Actualizado el',
                ],
            ],
        ],
    ],
];
