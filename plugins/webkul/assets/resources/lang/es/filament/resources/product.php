<?php

return [
    'navigation' => [
        'title' => 'Productos',
    ],

    'label'        => 'Producto',
    'plural-label' => 'Productos',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Información General',
                'fields' => [
                    'name'                 => 'Nombre del Producto',
                    'category'             => 'Categoría',
                    'type'                 => 'Tipo de Producto',
                    'type-asset-hint'      => 'Una unidad rastreada identificada por número de serie y custodio.',
                    'type-consumable-hint' => 'Un lote de cantidad emitido por unidades sin seguimiento de devolución.',
                    'model'                => 'Modelo',
                    'cost'                 => 'Costo Estándar',
                    'is-active'            => 'Activo',
                    'note'                 => 'Descripción / Especificaciones',
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

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Información General',
                'entries' => [
                    'name'      => 'Nombre del Producto',
                    'category'  => 'Categoría',
                    'type'      => 'Tipo de Producto',
                    'model'     => 'Modelo',
                    'cost'      => 'Costo Estándar',
                    'is-active' => 'Activo',
                    'note'      => 'Descripción / Especificaciones',
                ],
            ],

            'settings' => [
                'title'   => 'Configuración',
                'entries' => [
                    'company' => 'Empresa',
                ],
            ],

            'audit' => [
                'title'   => 'Información de Auditoría',
                'entries' => [
                    'creator'    => 'Creado por',
                    'created-at' => 'Creado el',
                    'updated-at' => 'Actualizado el',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                    => 'Nombre del Producto',
            'category'                => 'Categoría',
            'type'                    => 'Tipo de Producto',
            'model'                   => 'Modelo',
            'cost'                    => 'Costo',
            'assets-count'            => 'Activos Registrados',
            'available-assets-count'  => 'Disponible en almacén',
            'held-assets-count'       => 'Asignado en custodia',
            'terminated-assets-count' => 'Desguazado / Perdido',
            'total-cost'              => 'Valoración total',
            'is-active'               => 'Activo',
            'company'                 => 'Empresa',
            'deleted-at'              => 'Archivado el',
            'created-at'              => 'Creado el',
            'updated-at'              => 'Actualizado el',
        ],

        'groups' => [
            'category' => 'Categoría',
        ],

        'filters' => [
            'category'  => 'Categoría',
            'type'      => 'Tipo de Producto',
            'is-active' => 'Activo',
            'company'   => 'Empresa',
        ],
    ],

    'views' => [
        'active'   => 'Productos Activos',
        'archived' => 'Productos Archivados',
    ],

    'relation-manager' => [
        'assets' => [
            'title'   => 'Unidades de Activos Registradas',
            'actions' => [
                'open' => 'Ver Activo',
            ],
        ],
    ],
];
