<?php

return [
    'title' => 'Horarios de trabajo',

    'navigation' => [
        'title' => 'Horarios de trabajo',
        'group' => 'Empleado',
    ],

    'groups' => [
        'status'     => 'Estado',
        'created-by' => 'Creado por',
        'created-at' => 'Creado el',
        'updated-at' => 'Actualizado el',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Información general',
                'fields' => [
                    'name'                  => 'Nombre',
                    'schedule-name'         => 'Nombre del horario',
                    'schedule-name-tooltip' => 'Escriba un nombre descriptivo para el horario de trabajo.',
                    'timezone'              => 'Zona horaria',
                    'timezone-tooltip'      => 'Seleccione la zona horaria para el horario de trabajo.',
                    'company'               => 'Empresa',
                ],
            ],

            'configuration' => [
                'title'  => 'Configuración de horas de trabajo',
                'fields' => [
                    'hours-per-day'                   => 'Horas por día',
                    'hours-per-day-suffix'            => 'Horas',
                    'full-time-required-hours'        => 'Horas requeridas a tiempo completo',
                    'full-time-required-hours-suffix' => 'Horas por semana',
                ],
            ],

            'flexibility' => [
                'title'  => 'Flexibilidad',
                'fields' => [
                    'status'                     => 'Estado',
                    'two-weeks-calendar'         => 'Calendario de dos semanas',
                    'two-weeks-calendar-tooltip' => 'Activar el horario de trabajo alterno de dos semanas.',
                    'flexible-hours'             => 'Horario flexible',
                    'flexible-hours-tooltip'     => 'Permitir que los empleados tengan un horario de trabajo flexible.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'             => 'ID',
            'name'           => 'Nombre del horario',
            'timezone'       => 'Zona horaria',
            'company'        => 'Empresa',
            'flexible-hours' => 'Horario flexible',
            'status'         => 'Estado',
            'daily-hours'    => 'Horas diarias',
            'created-by'     => 'Creado por',
            'created-at'     => 'Creado el',
            'updated-at'     => 'Actualizado el',
        ],

        'filters' => [
            'company'           => 'Empresa',
            'is-active'         => 'Estado',
            'two-week-calendar' => 'Calendario de dos semanas',
            'flexible-hours'    => 'Horario flexible',
            'timezone'          => 'Zona horaria',
            'name'              => 'Nombre del horario',
            'attendance'        => 'Asistencia',
            'created-by'        => 'Creado por',
            'daily-hours'       => 'Horas diarias',
            'updated-at'        => 'Actualizado el',
            'created-at'        => 'Creado el',
        ],

        'groups' => [
            'name'           => 'Nombre del horario',
            'status'         => 'Estado',
            'timezone'       => 'Zona horaria',
            'flexible-hours' => 'Horario flexible',
            'daily-hours'    => 'Horas diarias',
            'created-by'     => 'Creado por',
            'created-at'     => 'Creado el',
            'updated-at'     => 'Actualizado el',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Plan de calendario restaurado',
                    'body'  => 'El plan de calendario se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Plan de calendario eliminado',
                    'body'  => 'El plan de calendario se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Plan de calendario eliminado permanentemente',
                    'body'  => 'El plan de calendario se ha eliminado permanentemente correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Planes de calendario restaurados',
                    'body'  => 'Los planes de calendario se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Planes de calendario eliminados',
                    'body'  => 'Los planes de calendario se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Planes de calendario eliminados permanentemente',
                    'body'  => 'Los planes de calendario se han eliminado permanentemente correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Información general',
                'entries' => [
                    'name'                  => 'Nombre',
                    'schedule-name'         => 'Nombre del horario',
                    'schedule-name-tooltip' => 'Escriba un nombre descriptivo para el horario de trabajo.',
                    'timezone'              => 'Zona horaria',
                    'timezone-tooltip'      => 'Seleccione la zona horaria para el horario de trabajo.',
                    'company'               => 'Empresa',
                ],
            ],

            'configuration' => [
                'title'   => 'Configuración de horas de trabajo',
                'entries' => [
                    'hours-per-day'                   => 'Horas por día',
                    'hours-per-day-suffix'            => ' Horas',
                    'full-time-required-hours'        => 'Horas requeridas a tiempo completo',
                    'full-time-required-hours-suffix' => ' Horas por semana',
                ],
            ],

            'flexibility' => [
                'title'   => 'Flexibilidad',
                'entries' => [
                    'status'                     => 'Estado',
                    'two-weeks-calendar'         => 'Calendario de dos semanas',
                    'two-weeks-calendar-tooltip' => 'Activar el horario de trabajo alterno de dos semanas.',
                    'flexible-hours'             => 'Horario flexible',
                    'flexible-hours-tooltip'     => 'Permitir que los empleados tengan un horario de trabajo flexible.',
                ],
            ],
        ],
    ],
];
