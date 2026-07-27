<?php

return [
    'title' => 'Horarios laborales',

    'navigation' => [
        'title' => 'Horarios laborales',
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
                    'schedule-name-tooltip' => 'Escriba un nombre descriptivo para el horario laboral.',
                    'timezone'              => 'Zona horaria',
                    'timezone-tooltip'      => 'Seleccione la zona horaria del horario laboral.',
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
                    'two-weeks-calendar-tooltip' => 'Habilitar un horario laboral alterno de dos semanas.',
                    'flexible-hours'             => 'Horas flexibles',
                    'flexible-hours-tooltip'     => 'Permitir que los empleados tengan horas de trabajo flexibles.',
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
            'flexible-hours' => 'Horas flexibles',
            'status'         => 'Estado',
            'daily-hours'    => 'Horas diarias',
            'created-by'     => 'Creado por',
            'created-at'     => 'Creado el',
            'updated-at'     => 'Actualizado el',
        ],

        'filters' => [
            'company'                  => 'Empresa',
            'is-active'                => 'Estado',
            'two-week-calendar'        => 'Calendario de dos semanas',
            'flexible-hours'           => 'Horas flexibles',
            'timezone'                 => 'Zona horaria',
            'name'                     => 'Nombre del horario',
            'attendance'               => 'Asistencia',
            'created-by'               => 'Creado por',
            'daily-hours'              => 'Horas diarias',
            'full-time-required-hours' => 'Horas requeridas a tiempo completo',
            'updated-at'               => 'Actualizado el',
            'created-at'               => 'Creado el',
        ],

        'groups' => [
            'name'           => 'Nombre del horario',
            'status'         => 'Estado',
            'timezone'       => 'Zona horaria',
            'flexible-hours' => 'Horas flexibles',
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
                    'schedule-name-tooltip' => 'Escriba un nombre descriptivo para el horario laboral.',
                    'timezone'              => 'Zona horaria',
                    'timezone-tooltip'      => 'Seleccione la zona horaria del horario laboral.',
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
                    'two-weeks-calendar-tooltip' => 'Habilitar un horario laboral alterno de dos semanas.',
                    'flexible-hours'             => 'Horas flexibles',
                    'flexible-hours-tooltip'     => 'Permitir que los empleados tengan horas de trabajo flexibles.',
                ],
            ],
        ],
    ],
];
