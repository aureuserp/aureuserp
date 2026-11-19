<?php

return [
    'title'        => 'Sitio de activos y custodias',
    'plural-title' => 'Sitios de activos y custodias',

    'navigation' => [
        'title' => 'Sitios de activos y custodias',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Detalles del sitio',
                'fields' => [
                    'name'              => 'Nombre',
                    'parent'            => 'Sitio principal',
                    'usage-type'        => 'Tipo de uso',
                    'usage-type-help'   => 'Las ubicaciones de activos fijos albergan activos instalados de la empresa; las zonas de custodia clasifican la custodia personal de los empleados.',
                    'code'              => 'Código',
                    'code-help'         => 'Código interno opcional, único dentro de la empresa.',
                    'type'              => 'Tipo',
                    'type-name'         => 'Nombre del tipo',
                    'type-icon'         => 'Icono',
                    'type-color'        => 'Color',
                    'note'              => 'Dirección / Ubicación y notas',
                    'note-placeholder'  => 'Edificio, piso, área de campo u otros detalles de ubicación.',
                ],
            ],

            'settings' => [
                'title'  => 'Configuración',
                'fields' => [
                    'company'                  => 'Empresa',
                    'default-custodian'        => 'Custodio predeterminado del sitio',
                    'default-custodian-help'   => 'Empleado asignado automáticamente como receptor de custodia cuando los activos se envían a este sitio.',
                    'is-active'                => 'Activo',
                    'requires-custodian'       => 'Requiere custodio',
                    'requires-custodian-help'  => 'Los sitios de campo y de vehículos requieren un receptor designado para cada entrega de custodia.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Nombre',
            'full-name'          => 'Nombre completo',
            'usage-type'         => 'Tipo de uso',
            'code'               => 'Código',
            'type'               => 'Tipo',
            'default-custodian'  => 'Custodio predeterminado',
            'is-active'          => 'Activo',
            'requires-custodian' => 'Requiere custodio',
            'assets-count'       => 'Activos',
            'total-cost'         => 'Valoración total',
            'company'            => 'Empresa',
            'creator'            => 'Creado por',
            'deleted-at'         => 'Archivado el',
            'created-at'         => 'Creado el',
            'updated-at'         => 'Actualizado el',
        ],

        'filters' => [
            'usage-type'         => 'Tipo de uso',
            'parent'             => 'Sitio principal',
            'type'               => 'Tipo',
            'is-active'          => 'Activo',
            'requires-custodian' => 'Requiere custodio',
            'company'            => 'Empresa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Sitio actualizado',
                    'body'  => 'El sitio se ha actualizado correctamente.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'No se pudo eliminar el sitio',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Detalles del sitio',
                'entries' => [
                    'name'               => 'Nombre',
                    'full-name'          => 'Nombre completo',
                    'parent'             => 'Sitio principal',
                    'usage-type'         => 'Tipo de uso',
                    'code'               => 'Código',
                    'type'               => 'Tipo',
                    'company'            => 'Empresa',
                    'is-active'          => 'Activo',
                    'requires-custodian' => 'Requiere custodio',
                    'default-custodian'  => 'Custodio predeterminado',
                    'note'               => 'Dirección / Ubicación y notas',
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
        'site-assets' => [
            'title'   => 'Contenido del sitio',
            'columns' => [
                'asset-tag'          => 'Etiqueta de activo',
                'asset-name'         => 'Activo',
                'category'           => 'Categoría',
                'current-site'       => 'Ubicación / Subsitio',
                'serial-no'          => 'N.º de serie',
                'receiver'           => 'Recibido por',
                'condition-out'      => 'Condición de salida',
                'condition-in'       => 'Condición de entrada',
                'cost'               => 'Coste',
                'issued-at'          => 'Emitido el',
                'expected-return-at' => 'Devolución prevista',
                'status'             => 'Estado',
                'ended-type'         => 'Finalizado como',
                'issued-by'          => 'Emitido por',
            ],
            'filters' => [
                'status' => 'Estado',
            ],
            'status' => [
                'open'   => 'En sitio',
                'closed' => 'Devuelto',
            ],
            'tabs' => [
                'all'           => 'Todos (incluidas sububicaciones)',
                'direct'        => 'Activos directos',
                'subsites'      => 'Sububicaciones',
                'with-children' => ':site (Todas)',
                'only'          => ':site (Solo directo)',
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'Nuevo sitio de activo',
                    'notification' => [
                        'title' => 'Sitio creado',
                        'body'  => 'El sitio se ha creado correctamente.',
                    ],
                ],
            ],
            'views' => [
                'all'             => 'Todos los sitios',
                'asset-locations' => 'Ubicaciones de activos fijos',
                'custody-zones'   => 'Zonas de custodia',
                'archived'        => 'Sitios archivados',
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'Sitio creado',
                'body'  => 'El sitio se ha creado correctamente.',
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'Sitio actualizado',
                'body'  => 'El sitio se ha actualizado correctamente.',
            ],
        ],
    ],
];
