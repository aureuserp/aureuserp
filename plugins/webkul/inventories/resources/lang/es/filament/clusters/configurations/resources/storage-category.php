<?php

return [
    'navigation' => [
        'title' => 'Categorías de almacenamiento',
        'group' => 'Gestión de almacenes',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'name'               => 'Nombre',
                    'allow-new-products' => 'Permitir nuevos productos',
                    'max-weight'         => 'Peso máximo',
                    'company'            => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Nombre',
            'allow-new-products' => 'Permitir nuevos productos',
            'max-weight'         => 'Peso máximo',
            'company'            => 'Empresa',
            'deleted-at'         => 'Eliminado el',
            'created-at'         => 'Creado el',
            'updated-at'         => 'Actualizado el',
        ],

        'groups' => [
            'allow-new-products' => 'Permitir nuevos productos',
            'created-at'         => 'Creado el',
            'updated-at'         => 'Actualizado el',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Categoría de almacenamiento eliminada',
                    'body'  => 'La categoría de almacenamiento ha sido eliminada correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Categorías de almacenamiento eliminadas',
                    'body'  => 'Las categorías de almacenamiento han sido eliminadas correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'entries' => [
                    'name'               => 'Nombre',
                    'allow-new-products' => 'Permitir nuevos productos',
                    'max-weight'         => 'Peso máximo',
                    'company'            => 'Empresa',
                ],
            ],

            'record-information' => [
                'title' => 'Información del registro',

                'entries' => [
                    'created-by'   => 'Creado por',
                    'created-at'   => 'Creado el',
                    'last-updated' => 'Última actualización',
                ],
            ],
        ],
    ],
];
