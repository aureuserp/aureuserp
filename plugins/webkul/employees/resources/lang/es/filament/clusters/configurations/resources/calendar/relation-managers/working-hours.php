<?php

return [
    'modal' => [
        'title' => 'Horas de Trabajo',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Información General',
                'fields' => [
                    'attendance-name' => 'Nombre de Asistencia',
                    'day-of-week'     => 'Día de la Semana',
                ],
            ],

            'timing-information' => [
                'title' => 'Información de Horarios',

                'fields' => [
                    'day-period' => 'Períodos del Día',
                    'week-type'  => 'Tipo de Semana',
                    'work-from'  => 'Trabajo Desde',
                    'work-to'    => 'Trabajo Hasta',
                ],
            ],

            'date-information' => [
                'title' => 'Información de Fecha',

                'fields' => [
                    'starting-date' => 'Fecha de Inicio',
                    'ending-date'   => 'Fecha de Fin',
                ],
            ],

            'additional-information' => [
                'title' => 'Información Adicional',

                'fields' => [
                    'durations-days' => 'Duración (Días)',
                    'display-type'   => 'Tipo de Visualización',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'          => 'Nombre de Asistencia',
            'day-of-week'   => 'Día de la Semana',
            'day-period'    => 'Períodos del Día',
            'work-from'     => 'Trabajo Desde',
            'work-to'       => 'Trabajo Hasta',
            'starting-date' => 'Fecha de Inicio',
            'ending-date'   => 'Fecha de Fin',
            'display-type'  => 'Tipo de Visualización',
            'created-by'    => 'Creado Por',
            'created-at'    => 'Creado El',
            'updated-at'    => 'Actualizado El',
        ],

        'groups' => [
            'activity-type' => 'Tipo de Actividad',
            'assignment'    => 'Asignación',
            'assigned-to'   => 'Asignado A',
            'interval'      => 'Intervalo',
            'delay-unit'    => 'Unidad de Retraso',
            'delay-from'    => 'Retraso Desde',
            'created-by'    => 'Creado Por',
            'created-at'    => 'Creado El',
            'updated-at'    => 'Actualizado El',
        ],

        'filters' => [
            'display-type' => 'Tipo de Visualización',
            'day-of-week'  => 'Día de la Semana',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Horas de Trabajo actualizadas',
                    'body'  => 'Las horas de trabajo se han actualizado exitosamente.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Horas de Trabajo creadas',
                    'body'  => 'Las horas de trabajo se han creado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Horas de Trabajo eliminadas',
                    'body'  => 'Las horas de trabajo se han eliminado exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Horas de Trabajo restauradas',
                    'body'  => 'Las horas de trabajo se han restaurado exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Horas de Trabajo eliminadas',
                    'body'  => 'Las horas de trabajo se han eliminado exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Horas de Trabajo restauradas',
                    'body'  => 'Las horas de trabajo se han restaurado exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Horas de Trabajo eliminadas permanentemente',
                    'body'  => 'Las horas de trabajo se han eliminado permanentemente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Información General',

                'entries' => [
                    'name'        => 'Nombre de Asistencia',
                    'day-of-week' => 'Día de la Semana',
                ],
            ],

            'timing-information' => [
                'title' => 'Información de Horarios',

                'entries' => [
                    'day-period' => 'Períodos del Día',
                    'week-type'  => 'Tipo de Semana',
                    'work-from'  => 'Trabajo Desde',
                    'work-to'    => 'Trabajo Hasta',
                ],
            ],

            'date-information' => [
                'title' => 'Información de Fecha',

                'entries' => [
                    'starting-date' => 'Fecha de Inicio',
                    'ending-date'   => 'Fecha de Fin',
                ],
            ],

            'additional-information' => [
                'title' => 'Información Adicional',

                'entries' => [
                    'durations-days' => 'Duración (Días)',
                    'display-type'   => 'Tipo de Visualización',
                ],
            ],
        ],

        'note' => 'Nota',
    ],
];
