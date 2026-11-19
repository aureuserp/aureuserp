<?php

return [
    'title'        => 'Categoría de activos',
    'plural-title' => 'Categorías de activos',

    'navigation' => [
        'title' => 'Categorías de activos',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Detalles de la categoría',
                'fields' => [
                    'name'   => 'Nombre de la categoría',
                    'parent' => 'Categoría superior',
                ],
            ],

            'settings' => [
                'title'  => 'Configuración',
                'fields' => [
                    'company' => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Nombre',
            'full-name'      => 'Ruta completa',
            'parent'         => 'Categoría superior',
            'products-count' => 'Productos',
            'assets-count'   => 'Activos',
            'total-cost'     => 'Valor total',
            'company'        => 'Empresa',
            'creator'        => 'Creado por',
            'created-at'     => 'Creado el',
            'updated-at'     => 'Actualizado el',
        ],

        'filters' => [
            'parent'  => 'Categoría superior',
            'company' => 'Empresa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Categoría actualizada',
                    'body'  => 'La categoría se ha actualizado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Detalles de la categoría',
                'entries' => [
                    'name'           => 'Nombre',
                    'full-name'      => 'Ruta completa',
                    'parent'         => 'Categoría superior',
                    'products-count' => 'Productos',
                    'assets-count'   => 'Activos',
                    'total-cost'     => 'Valor total',
                ],
            ],

            'audit' => [
                'title'   => 'Auditoría',
                'entries' => [
                    'creator'    => 'Creado por',
                    'created-at' => 'Creado el',
                    'updated-at' => 'Actualizado el',
                ],
            ],
        ],
    ],

    'relation-manager' => [
        'category-products' => [
            'title' => 'Productos de esta categoría',
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'Nueva categoría',
                    'notification' => [
                        'title' => 'Categoría creada',
                        'body'  => 'La categoría se ha creado correctamente.',
                    ],
                ],
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'Categoría creada',
                'body'  => 'La categoría se ha creado correctamente.',
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'Categoría actualizada',
                'body'  => 'La categoría se ha actualizado correctamente.',
            ],
        ],
    ],
];
