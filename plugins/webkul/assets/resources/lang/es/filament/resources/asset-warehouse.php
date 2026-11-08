<?php

return [
    'title'        => 'Almacén de activos',
    'plural-title' => 'Almacenes de activos',

    'navigation' => [
        'title' => 'Almacenes',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Detalles del almacén',
                'fields' => [
                    'name'             => 'Nombre',
                    'code'             => 'Código',
                    'code-help'        => 'Código interno opcional, único en la empresa.',
                    'note'             => 'Dirección / Ubicación y notas',
                    'note-placeholder' => 'Ubicación o notas de almacenamiento.',
                ],
            ],

            'settings' => [
                'title'  => 'Configuración',
                'fields' => [
                    'company'        => 'Empresa',
                    'custodian'      => 'Encargado del almacén',
                    'custodian-help' => 'Empleado a cargo de los activos almacenados en este almacén.',
                    'is-active'      => 'Activo',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nombre',
            'code'         => 'Código',
            'custodian'    => 'Encargado del almacén',
            'assets-count' => 'Activos almacenados',
            'total-cost'   => 'Valoración total',
            'is-active'    => 'Activo',
            'company'      => 'Empresa',
            'creator'      => 'Creado por',
            'deleted-at'   => 'Archivado el',
            'created-at'   => 'Fecha de creación',
            'updated-at'   => 'Fecha de actualización',
        ],

        'filters' => [
            'custodian' => 'Encargado del almacén',
            'is-active' => 'Activo',
            'company'   => 'Empresa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Almacén actualizado',
                    'body'  => 'El almacén se ha actualizado correctamente.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'No se pudo eliminar el almacén',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Detalles del almacén',
                'entries' => [
                    'name'      => 'Nombre',
                    'code'      => 'Código',
                    'custodian' => 'Encargado del almacén',
                    'company'   => 'Empresa',
                    'is-active' => 'Activo',
                    'note'      => 'Dirección / Ubicación y notas',
                ],
            ],

            'audit' => [
                'title'   => 'Auditoría',
                'entries' => [
                    'creator'    => 'Creado por',
                    'created-at' => 'Fecha de creación',
                    'updated-at' => 'Fecha de actualización',
                ],
            ],
        ],
    ],

    'relation-manager' => [
        'warehouse-assets' => [
            'title'   => 'Activos almacenados',
            'columns' => [
                'asset-tag'     => 'Etiqueta del activo',
                'asset-name'    => 'Nombre del activo',
                'product'       => 'Producto',
                'model'         => 'Modelo',
                'serial-no'     => 'Número de serie',
                'category'      => 'Categoría',
                'condition'     => 'Condición',
                'cost'          => 'Coste',
                'purchase-date' => 'Fecha de compra',
            ],
            'actions' => [
                'issue' => 'Entregar activo',
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'Nuevo almacén de activos',
                    'notification' => [
                        'title' => 'Almacén creado',
                        'body'  => 'El almacén se ha creado correctamente.',
                    ],
                ],
            ],
            'views' => [
                'all'      => 'Todos los almacenes',
                'archived' => 'Almacenes archivados',
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'Almacén creado',
                'body'  => 'El almacén se ha creado correctamente.',
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'Almacén actualizado',
                'body'  => 'El almacén se ha actualizado correctamente.',
            ],
        ],
    ],
];
