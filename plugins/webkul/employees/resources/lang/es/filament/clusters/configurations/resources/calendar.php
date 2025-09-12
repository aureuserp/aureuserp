<?php

return [
    'title' => 'Horarios de Trabajo',

    'navigation' => [
        'title' => 'Horarios de Trabajo',
        'group' => 'Empleado',
    ],

    'groups' => [
        'status'     => 'Estado',
        'created-by' => 'Creado Por',
        'created-at' => 'Creado El',
        'updated-at' => 'Actualizado El',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Información General',
                'fields' => [
                    'name'                    => 'Nombre',
                    'schedule-name'           => 'Nombre del Horario',
                    'schedule-name-tooltip' => 'Por favor, escriba un nombre descriptivo para el horario de trabajo.',
                    'timezone'                => 'Zona Horaria',
                    'timezone-tooltip'      => 'Por favor, seleccione la zona horaria para el horario de trabajo.',
                    'company'                 => 'Empresa',
                ],
            ],

            'configuration' => [
                'title'  => 'Configuración de Horas de Trabajo',
                'fields' => [
                    'hours-per-day'                 => 'Horas Por Día',
                    'hours-per-day-suffix'          => 'Horas',
                    'full-time-required-hours'      => 'Horas Requeridas Tiempo Completo',
                    'full-time-required-hours-suffix' => 'Horas Por Semana',
                ],
            ],

            'flexibility' => [
                'title'  => 'Flexibilidad',
                'fields' => [
                    'status'                    => 'Estado',
                    'two-weeks-calendar'          => 'Calendario de Dos Semanas',
                    'two-weeks-calendar-tooltip' => 'Habilita un horario de trabajo alternando cada dos semanas.',
                    'flexible-hours'              => 'Horario Flexible',
                    'flexible-hours-tooltip'    => 'Permite a los empleados tener horarios de trabajo flexibles.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'             => 'ID',
            'name'           => 'Nombre del Horario',
            'timezone'       => 'Zona Horaria',
            'company'        => 'Empresa',
            'flexible-hours' => 'Horario Flexible',
            'status'         => 'Estado',
            'daily-hours'    => 'Horas Diarias',
            'created-by'     => 'Creado Por',
            'created-at'     => 'Creado El',
            'updated-at'     => 'Actualizado El',
        ],

        'filters' => [
            'company'           => 'Empresa',
            'is-active'         => 'Estado',
            'two-week-calendar' => 'Calendario de Dos Semanas',
            'flexible-hours'    => 'Horario Flexible',
            'timezone'          => 'Zona Horaria',
            'name'              => 'Nombre del Horario',
            'attendance'        => 'Asistencia',
            'created-by'        => 'Creado Por',
            'daily-hours'       => 'Horas Diarias',
            'updated-at'        => 'Actualizado El',
            'created-at'        => 'Creado El',
        ],

        'groups' => [
            'name'           => 'Nombre del Horario',
            'status'         => 'Estado',
            'timezone'       => 'Zona Horaria',
            'flexible-hours' => 'Horario Flexible',
            'daily-hours'    => 'Horas Diarias',
            'created-by'     => 'Creado Por',
            'created-at'     => 'Creado El',
            'updated-at'     => 'Actualizado El',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Plan de Calendario restaurado',
                    'body'  => 'El plan de calendario ha sido restaurado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Plan de Calendario eliminado',
                    'body'  => 'El plan de calendario ha sido eliminado exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Plan de Calendario eliminado permanentemente',
                    'body'  => 'El plan de calendario ha sido eliminado permanentemente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Planes de Calendario restaurados',
                    'body'  => 'Los planes de calendario han sido restaurados exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Planes de Calendario eliminados',
                    'body'  => 'Los planes de calendario han sido eliminados exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Planes de Calendario eliminados permanentemente',
                    'body'  => 'Los planes de calendario han sido eliminados permanentemente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Información General',
                'entries' => [
                    'name'                    => 'Nombre',
                    'schedule-name'           => 'Nombre del Horario',
                    'schedule-name-tooltip' => 'Por favor, escriba un nombre descriptivo para el horario de trabajo.',
                    'timezone'                => 'Zona Horaria',
                    'timezone-tooltip'      => 'Por favor, seleccione la zona horaria para el horario de trabajo.',
                    'company'                 => 'Empresa',
                ],
            ],

            'configuration' => [
                'title'   => 'Configuración de Horas de Trabajo',
                'entries' => [
                    'hours-per-day'                 => 'Horas Por Día',
                    'hours-per-day-suffix'          => 'Horas',
                    'full-time-required-hours'      => 'Horas Requeridas Tiempo Completo',
                    'full-time-required-hours-suffix' => 'Horas Por Semana',
                ],
            ],

            'flexibility' => [
                'title'   => 'Flexibilidad',
                'entries' => [
                    'status'                    => 'Estado',
                    'two-weeks-calendar'          => 'Calendario de Dos Semanas',
                    'two-weeks-calendar-tooltip' => 'Habilita un horario de trabajo alternando cada dos semanas.',
                    'flexible-hours'              => 'Horario Flexible',
                    'flexible-hours-tooltip'    => 'Permite a los empleados tener horarios de trabajo flexibles.',
                ],
            ],
        ],
    ],
];
