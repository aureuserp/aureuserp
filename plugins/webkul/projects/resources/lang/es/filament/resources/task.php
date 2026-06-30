<?php

return [
    'title' => 'Tareas',

    'navigation' => [
        'title' => 'Tareas',
        'group' => 'Proyecto',
    ],

    'global-search' => [
        'project'   => 'Proyecto',
        'customer'  => 'Cliente',
        'milestone' => 'Hito',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'title'             => 'Título',
                    'title-placeholder' => 'Título de la tarea...',
                    'tags'              => 'Etiquetas',
                    'name'              => 'Nombre',
                    'color'             => 'Color',
                    'description'       => 'Descripción',
                    'project'           => 'Proyecto',
                    'status'            => 'Estado',
                    'start_date'        => 'Fecha de inicio',
                    'end_date'          => 'Fecha de fin',
                ],
            ],

            'additional' => [
                'title' => 'Información adicional',
            ],

            'settings' => [
                'title' => 'Configuración',

                'fields' => [
                    'project'                     => 'Proyecto',
                    'milestone'                   => 'Hito',
                    'milestone-hint-text'         => 'Entregar automáticamente los servicios al alcanzar un hito vinculándolo a una línea de pedido de venta.',
                    'name'                        => 'Nombre',
                    'deadline'                    => 'Fecha límite',
                    'is-completed'                => 'Completada',
                    'customer'                    => 'Cliente',
                    'assignees'                   => 'Asignados',
                    'allocated-hours'             => 'Horas asignadas',
                    'allocated-hours-helper-text' => 'En horas (Ej. 1,5 horas significa 1 hora 30 minutos)',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                  => 'ID',
            'priority'            => 'Prioridad',
            'state'               => 'Estado',
            'new-state'           => 'Nuevo estado',
            'update-state'        => 'Actualizar estado',
            'title'               => 'Título',
            'project'             => 'Proyecto',
            'project-placeholder' => 'Tarea privada',
            'milestone'           => 'Hito',
            'customer'            => 'Cliente',
            'assignees'           => 'Asignados',
            'allocated-time'      => 'Tiempo asignado',
            'time-spent'          => 'Tiempo dedicado',
            'time-remaining'      => 'Tiempo restante',
            'progress'            => 'Progreso',
            'deadline'            => 'Fecha límite',
            'tags'                => 'Etiquetas',
            'stage'               => 'Etapa',
        ],

        'groups' => [
            'state'      => 'Estado',
            'project'    => 'Proyecto',
            'milestone'  => 'Hito',
            'customer'   => 'Cliente',
            'deadline'   => 'Fecha límite',
            'stage'      => 'Etapa',
            'created-at' => 'Creado el',
        ],

        'filters' => [
            'title'             => 'Título',
            'priority'          => 'Prioridad',
            'low'               => 'Baja',
            'high'              => 'Alta',
            'state'             => 'Estado',
            'tags'              => 'Etiquetas',
            'allocated-hours'   => 'Horas asignadas',
            'total-hours-spent' => 'Total de horas dedicadas',
            'remaining-hours'   => 'Horas restantes',
            'overtime'          => 'Horas extra',
            'progress'          => 'Progreso',
            'deadline'          => 'Fecha límite',
            'created-at'        => 'Creado el',
            'updated-at'        => 'Actualizado el',
            'assignees'         => 'Asignados',
            'customer'          => 'Cliente',
            'project'           => 'Proyecto',
            'stage'             => 'Etapa',
            'milestone'         => 'Hito',
            'company'           => 'Empresa',
            'creator'           => 'Creador',
        ],

        'actions' => [
            'update-state' => [
                'modal-heading' => 'Actualizar estado de la tarea',
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Tarea restaurada',
                    'body'  => 'La tarea se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tarea eliminada',
                    'body'  => 'La tarea se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tarea eliminada permanentemente',
                    'body'  => 'La tarea se ha eliminado permanentemente correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tareas restauradas',
                    'body'  => 'Las tareas se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tareas eliminadas',
                    'body'  => 'Las tareas se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tareas eliminadas permanentemente',
                    'body'  => 'Las tareas se han eliminado permanentemente correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'title'       => 'Título',
                    'state'       => 'Estado',
                    'tags'        => 'Etiquetas',
                    'priority'    => 'Prioridad',
                    'description' => 'Descripción',
                ],
            ],

            'project-information' => [
                'title' => 'Información del proyecto',

                'entries' => [
                    'project'   => 'Proyecto',
                    'milestone' => 'Hito',
                    'customer'  => 'Cliente',
                    'assignees' => 'Asignados',
                    'deadline'  => 'Fecha límite',
                    'stage'     => 'Etapa',
                ],
            ],

            'time-tracking' => [
                'title' => 'Seguimiento del tiempo',

                'entries' => [
                    'allocated-time'        => 'Tiempo asignado',
                    'time-spent'            => 'Tiempo dedicado',
                    'time-spent-suffix'     => ' Horas',
                    'time-remaining'        => 'Tiempo restante',
                    'time-remaining-suffix' => ' Horas',
                    'progress'              => 'Progreso',
                ],
            ],

            'additional-information' => [
                'title' => 'Información adicional',
            ],

            'record-information' => [
                'title' => 'Información del registro',

                'entries' => [
                    'created-at'   => 'Creado el',
                    'created-by'   => 'Creado por',
                    'last-updated' => 'Última actualización',
                ],
            ],

            'statistics' => [
                'title' => 'Estadísticas',

                'entries' => [
                    'sub-tasks'         => 'Subtareas',
                    'timesheet-entries' => 'Entradas de parte de horas',
                ],
            ],
        ],
    ],
];
