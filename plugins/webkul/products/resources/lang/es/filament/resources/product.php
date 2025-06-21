<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'             => 'Nombre',
                    'name-placeholder' => 'ej. Camiseta',
                    'description'      => 'Descripción',
                    'tags'             => 'Etiquetas',
                ],
            ],

            'images' => [
                'title' => 'Imágenes',
            ],

            'inventory' => [
                'title' => 'Inventario',

                'fields' => [],

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Logística',

                        'fields' => [
                            'weight' => 'Peso',
                            'volume' => 'Volumen',
                        ],
                    ],
                ],
            ],

            'settings' => [
                'title' => 'Configuración',

                'fields' => [
                    'type'      => 'Tipo',
                    'reference' => 'Referencia',
                    'barcode'   => 'Código de Barras',
                    'category'  => 'Categoría',
                    'company'   => 'Empresa',
                ],
            ],

            'pricing' => [
                'title' => 'Precios',

                'fields' => [
                    'price' => 'Precio',
                    'cost'  => 'Costo',
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
            'variants'    => 'Variantes',
            'images'      => 'Imágenes',
            'type'        => 'Tipo',
            'reference'   => 'Referencia',
            'responsible' => 'Responsable',
            'barcode'     => 'Código de Barras',
            'category'    => 'Categoría',
            'company'     => 'Empresa',
            'price'       => 'Precio',
            'cost'        => 'Costo',
            'on-hand'     => 'En Existencia',
            'tags'        => 'Etiquetas',
            'deleted-at'  => 'Eliminado El',
            'created-at'  => 'Creado El',
            'updated-at'  => 'Actualizado El',
        ],

        'groups' => [
            'type'       => 'Tipo',
            'category'   => 'Categoría',
            'created-at' => 'Creado El',
        ],

        'filters' => [
            'name'        => 'Nombre',
            'type'        => 'Tipo',
            'reference'   => 'Referencia',
            'barcode'     => 'Código de Barras',
            'category'    => 'Categoría',
            'company'     => 'Empresa',
            'price'       => 'Precio',
            'cost'        => 'Costo',
            'is-favorite' => 'Es Favorito',
            'weight'      => 'Peso',
            'volume'      => 'Volumen',
            'tags'        => 'Etiquetas',
            'responsible' => 'Responsable',
            'created-at'  => 'Creado El',
            'updated-at'  => 'Actualizado El',
            'creator'     => 'Creador',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Producto restaurado',
                    'body'  => 'El producto ha sido restaurado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Producto eliminado',
                    'body'  => 'El producto ha sido eliminado exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Producto eliminado permanentemente',
                        'body'  => 'El producto ha sido eliminado permanentemente exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el producto',
                        'body'  => 'El producto no puede ser eliminado porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Imprimir Etiquetas',

                'form' => [
                    'fields' => [
                        'quantity' => 'Número de Etiquetas',
                        'format'   => 'Formato',

                        'format-options' => [
                            'dymo'       => 'Dymo',
                            '2x7_price'  => '2x7 con precio',
                            '4x7_price'  => '4x7 con precio',
                            '4x12'       => '4x12',
                            '4x12_price' => '4x12 con precio',
                        ],
                    ],
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Productos restaurados',
                    'body'  => 'Los productos han sido restaurados exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Productos eliminados',
                    'body'  => 'Los productos han sido eliminados exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Productos eliminados permanentemente',
                        'body'  => 'Los productos han sido eliminados permanentemente exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los productos',
                        'body'  => 'Los productos no pueden ser eliminados porque están actualmente en uso.',
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
                    'barcode'   => 'Código de Barras',
                    'category'  => 'Categoría',
                    'company'   => 'Empresa',
                ],
            ],

            'pricing' => [
                'title' => 'Precios',

                'entries' => [
                    'price' => 'Precio',
                    'cost'  => 'Costo',
                ],
            ],

            'inventory' => [
                'title' => 'Inventario',

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Logística',

                        'entries' => [
                            'weight' => 'Peso',
                            'volume' => 'Volumen',
                        ],
                    ],
                ],
            ],

            'record-information' => [
                'title' => 'Información del Registro',

                'entries' => [
                    'created-at' => 'Creado El',
                    'created-by' => 'Creado Por',
                    'updated-at' => 'Actualizado El',
                ],
            ],
        ],
    ],
];
