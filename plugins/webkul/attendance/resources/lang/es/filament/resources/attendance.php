<?php

return [
    'title' => 'Asistencias',

    'navigation' => [
        'title' => 'Asistencias',
    ],

    'form' => [
        'employee'                   => 'Empleado',
        'work-date'                  => 'Fecha de trabajo',
        'duplicate-day'              => ':employee ya tiene un registro de asistencia el :date.',
        'check-in'                   => 'Entrada',
        'check-in-not-in-work-date'  => 'La entrada debe estar dentro de la fecha de trabajo.',
        'check-out'                  => 'Salida',
        'check-out-before-check-in'  => 'La salida debe ser posterior a la entrada.',
        'check-out-not-in-work-date' => 'La salida debe estar dentro de la fecha de trabajo.',
        'long-shift-warning'         => 'Aviso: este turno dura :hours horas.',
        'source'                     => 'Fuente',
        'source-manual'              => 'Manual',
    ],

    'table' => [
        'columns' => [
            'employee'   => 'Empleado',
            'work-date'  => 'Fecha de trabajo',
            'check-in'   => 'Entrada',
            'check-out'  => 'Salida',
            'worked'     => 'Trabajado',
            'source'     => 'Fuente',
            'created-at' => 'Creado el',
        ],

        'filters' => [
            'employee'          => 'Empleado',
            'source'            => 'Fuente',
            'work-date'         => 'Fecha de trabajo',
            'preset'            => 'Período rápido',
            'preset-today'      => 'Hoy',
            'preset-this-week'  => 'Esta semana',
            'preset-this-month' => 'Este mes',
            'date-from'         => 'Desde',
            'date-to'           => 'Hasta',
        ],

        'toolbar-actions' => [
            'export' => 'Exportar',
        ],

        'record-actions' => [
            'add-checkout' => 'Añadir salida',
        ],
    ],
];
