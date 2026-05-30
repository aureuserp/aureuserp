<?php

return [
    'global-search' => [
        'reference' => 'Referencia',
        'barcode'   => 'Código de barras',
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
                    'barcode'   => 'Código de barras',
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
            'favorite'        => 'Favorito',
            'name'            => 'Nombre',
            'variants'        => 'Variantes',
            'images'          => 'Imágenes',
            'type'            => 'Tipo',
            'reference'       => 'Referencia',
            'responsible'     => 'Responsable',
            'barcode'         => 'Código de barras',
            'category'        => 'Categoría',
            'company'         => 'Empresa',
            'price'           => 'Precio',
            'cost'            => 'Costo',
            'on-hand'         => 'En existencia',
            'tags'            => 'Etiquetas',
            'deleted-at'      => 'Eliminado el',
            'created-at'      => 'Creado el',
            'updated-at'      => 'Actualizado el',
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
            'cost'        => 'Costo',
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
                    'body'  => 'El producto ha sido restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Producto eliminado',
                    'body'  => 'El producto ha sido eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Producto eliminado definitivamente',
                        'body'  => 'El producto ha sido eliminado permanentemente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el producto',
                        'body'  => 'El producto no puede eliminarse porque está en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Imprimir etiquetas',

                'form' => [
                    'fields' => [
                        'quantity' => 'Número de etiquetas',
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
                    'body'  => 'Los productos han sido restaurados correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Productos eliminados',
                    'body'  => 'Los productos han sido eliminados correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Productos eliminados definitivamente',
                        'body'  => 'Los productos han sido eliminados definitivamente de forma correcta.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los productos',
                        'body'  => 'Los productos no pueden eliminarse porque están en uso.',
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
                    'barcode'   => 'Código de barras',
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
