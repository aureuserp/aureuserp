<?php

return [
    'navigation' => [
        'title' => 'Centros de trabajo',
        'group' => 'Configuración',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'name'                     => 'Nombre',
                    'name-placeholder'         => 'p. ej. Línea de ensamble 1',
                    'code'                     => 'Código',
                    'code-placeholder'         => 'p. ej. LE1',
                    'working-state'            => 'Estado operativo',
                    'color'                    => 'Color',
                    'tags'                     => 'Etiqueta',
                    'alternative-work-centers' => 'Centros de trabajo alternativos',
                    'company'                  => 'Empresa',
                    'calendar'                 => 'Horas operativas',
                ],
            ],

            'information' => [
                'title'     => 'Información general',
                'fieldsets' => [
                    'production-information' => 'Información de producción',
                    'costing-information'    => 'Información de costos',
                ],
                'fields' => [
                    'default-capacity' => 'Capacidad predeterminada',
                    'time-efficiency'  => 'Eficiencia del tiempo',
                    'oee-target'       => 'Objetivo OEE',
                    'costs-per-hour'   => 'Costo por hora',
                    'cost-suffix'      => 'por hora',
                    'setup-time'       => 'Tiempo de preparación',
                    'cleanup-time'     => 'Tiempo de limpieza',
                    'time-suffix'      => 'minutos',
                ],
            ],

            'description' => [
                'title'  => 'Descripción',
                'fields' => [
                    'note'             => 'Descripción',
                    'note-placeholder' => 'Descripción del centro de trabajo...',
                ],
            ],

            'specific-capacity' => [
                'title'  => 'Capacidad específica',
                'fields' => [
                    'records' => 'Capacidad específica',
                ],
                'columns' => [
                    'product'      => 'Producto',
                    'product-uom'  => 'UOM',
                    'capacity'     => 'Capacidad',
                    'setup-time'   => 'Tiempo de preparación',
                    'cleanup-time' => 'Tiempo de limpieza',
                ],
                'actions' => [
                    'add' => 'Agregar una línea',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'Nombre',
            'code'             => 'Código',
            'company'          => 'Empresa',
            'calendar'         => 'Horas operativas',
            'working-state'    => 'Estado operativo',
            'default-capacity' => 'Capacidad',
            'time-efficiency'  => 'Eficiencia',
            'costs-per-hour'   => 'Costo por hora',
            'deleted-at'       => 'Eliminado el',
            'created-at'       => 'Creado el',
            'updated-at'       => 'Actualizado el',
        ],

        'groups' => [
            'company' => 'Empresa',
        ],

        'filters' => [
            'company'       => 'Empresa',
            'working-state' => 'Estado operativo',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Centro de trabajo restaurado',
                    'body'  => 'El centro de trabajo ha sido restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Centro de trabajo archivado',
                    'body'  => 'El centro de trabajo ha sido archivado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Centro de trabajo eliminado',
                        'body'  => 'El centro de trabajo ha sido eliminado permanentemente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el centro de trabajo',
                        'body'  => 'No es posible eliminar el centro de trabajo porque está en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Centros de trabajo restaurados',
                    'body'  => 'Los centros de trabajo seleccionados han sido restaurados correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Centros de trabajo archivados',
                    'body'  => 'Los centros de trabajo seleccionados han sido archivados correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Centros de trabajo eliminados',
                        'body'  => 'Los centros de trabajo seleccionados han sido eliminados permanentemente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los centros de trabajo',
                        'body'  => 'Uno o más centros de trabajo seleccionados están en uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Información general',

                'entries' => [
                    'name'                     => 'Nombre del centro de trabajo',
                    'code'                     => 'Código',
                    'working-state'            => 'Estado operativo',
                    'tags'                     => 'Etiqueta',
                    'alternative-work-centers' => 'Centros de trabajo alternativos',
                    'company'                  => 'Empresa',
                    'calendar'                 => 'Horas operativas',
                ],
            ],

            'information' => [
                'title'     => 'Información general',
                'fieldsets' => [
                    'production-information' => 'Información de producción',
                    'costing-information'    => 'Información de costos',
                ],

                'entries' => [
                    'default-capacity' => 'Capacidad predeterminada',
                    'time-efficiency'  => 'Eficiencia del tiempo',
                    'oee-target'       => 'Objetivo OEE',
                    'costs-per-hour'   => 'Costo por hora',
                    'cost-suffix'      => 'por centro de trabajo',
                    'setup-time'       => 'Tiempo de preparación',
                    'cleanup-time'     => 'Tiempo de limpieza',
                    'time-suffix'      => 'minutos',
                ],
            ],

            'description' => [
                'title'   => 'Descripción',
                'entries' => [
                    'note' => 'Descripción',
                ],
            ],

            'specific-capacity' => [
                'title'   => 'Capacidades específicas',
                'columns' => [
                    'product'      => 'Producto',
                    'product-uom'  => 'UOM',
                    'capacity'     => 'Capacidad',
                    'setup-time'   => 'Tiempo de preparación',
                    'cleanup-time' => 'Tiempo de limpieza',
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
