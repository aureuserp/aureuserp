<?php

return [
    'modal' => [
        'title' => 'Horario laboral',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Información general',
                'fields' => [
                    'attendance-name' => 'Nombre de asistencia',
                    'day-of-week'     => 'Día de la semana',
                ],
            ],

            'timing-information' => [
                'title' => 'Información de horario',

                'fields' => [
                    'day-period' => 'Periodos del día',
                    'week-type'  => 'Tipo de semana',
                    'work-from'  => 'Trabajo desde',
                    'work-to'    => 'Trabajo hasta',
                ],
            ],

            'date-information' => [
                'title' => 'Información de fecha',

                'fields' => [
                    'starting-date' => 'Fecha de inicio',
                    'ending-date'   => 'Fecha de fin',
                ],
            ],

            'additional-information' => [
                'title' => 'Información adicional',

                'fields' => [
                    'durations-days' => 'Duración (días)',
                    'display-type'   => 'Tipo de visualización',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'          => 'Nombre de asistencia',
            'day-of-week'   => 'Día de la semana',
            'day-period'    => 'Periodos del día',
            'work-from'     => 'Trabajo desde',
            'work-to'       => 'Trabajo hasta',
            'starting-date' => 'Fecha de inicio',
            'ending-date'   => 'Fecha de fin',
            'display-type'  => 'Tipo de visualización',
            'created-by'    => 'Creado por',
            'created-at'    => 'Creado el',
            'updated-at'    => 'Actualizado el',
        ],

        'groups' => [
            'activity-type' => 'Tipo de actividad',
            'assignment'    => 'Asignación',
            'assigned-to'   => 'Asignado a',
            'interval'      => 'Intervalo',
            'delay-unit'    => 'Unidad de retraso',
            'delay-from'    => 'Retraso desde',
            'created-by'    => 'Creado por',
            'created-at'    => 'Creado el',
            'updated-at'    => 'Actualizado el',
        ],

        'filters' => [
            'display-type' => 'Tipo de visualización',
            'day-of-week'  => 'Día de la semana',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Horario laboral actualizado',
                    'body'  => 'El horario laboral se ha actualizado correctamente.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Horario laboral creado',
                    'body'  => 'El horario laboral se ha creado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Horario laboral eliminado',
                    'body'  => 'El horario laboral se ha eliminado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Horario laboral restaurado',
                    'body'  => 'El horario laboral se ha restaurado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Horario laboral eliminado',
                    'body'  => 'El horario laboral se ha eliminado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Horario laboral eliminado',
                    'body'  => 'El horario laboral se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Horario laboral eliminado',
                    'body'  => 'El horario laboral se ha eliminado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Información general',

                'entries' => [
                    'name'        => 'Nombre de asistencia',
                    'day-of-week' => 'Día de la semana',
                ],
            ],

            'timing-information' => [
                'title' => 'Información de horario',

                'entries' => [
                    'day-period' => 'Periodos del día',
                    'week-type'  => 'Tipo de semana',
                    'work-from'  => 'Trabajo desde',
                    'work-to'    => 'Trabajo hasta',
                ],
            ],

            'date-information' => [
                'title' => 'Información de fecha',

                'entries' => [
                    'starting-date' => 'Fecha de inicio',
                    'ending-date'   => 'Fecha de fin',
                ],
            ],

            'additional-information' => [
                'title' => 'Información adicional',

                'entries' => [
                    'durations-days' => 'Duración (días)',
                    'display-type'   => 'Tipo de visualización',
                ],
            ],
        ],

        'note' => 'Nota',
    ],
];
