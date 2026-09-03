<?php

return [
    'title' => 'Ausencias',

    'model-label' => 'Ausencias',

    'navigation' => [
        'title' => 'Ausencias',
    ],

    'global-search' => [
        'employee'      => 'Empleado',
        'time-off-type' => 'Tipo de ausencia',
        'date-from'     => 'Fecha desde',
        'date-to'       => 'Fecha hasta',
    ],

    'form' => [
        'fields' => [
            'employee-name'     => 'Nombre del empleado',
            'department-name'   => 'Nombre del departamento',
            'time-off-type'     => 'Tipo de ausencia',
            'date'              => 'Fecha',
            'dates'             => 'Fechas',
            'request-date-from' => 'Fecha de solicitud desde',
            'request-date-to'   => 'Fecha de solicitud hasta',
            'description'       => 'Descripción',
            'period'            => 'Período',
            'half-day'          => 'Medio día',
            'requested-days'    => 'Solicitado (días/horas)',
            'description'       => 'Descripción',
            'attachment'        => 'Adjunto',
            'day'               => ':day día',
            'days'              => ':days día(s)',
        ],
    ],

    'table' => [
        'columns' => [
            'employee-name'  => 'Empleado',
            'time-off-type'  => 'Tipo de ausencia',
            'description'    => 'Descripción',
            'date-from'      => 'Fecha desde',
            'date-to'        => 'Fecha hasta',
            'duration'       => 'Duración',
            'status'         => 'Estado',
        ],

        'groups' => [
            'employee-name' => 'Empleado',
            'time-off-type' => 'Tipo de ausencia',
            'status'        => 'Estado',
            'start-date'    => 'Fecha de inicio',
            'start-to'      => 'Fecha de fin',
            'updated-at'    => 'Actualizado el',
            'created-at'    => 'Creado el',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Validar',
                    'approve'  => 'Aprobar',
                ],
                'notification' => [
                    'title' => 'Ausencia aprobada',
                    'body'  => 'La ausencia se ha aprobado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Ausencia eliminada',
                    'body'  => 'La ausencia se ha eliminado correctamente.',
                ],
            ],

            'refused' => [
                'title'        => 'Rechazar',
                'notification' => [
                    'title' => 'Ausencia rechazada',
                    'body'  => 'La ausencia se ha rechazado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Ausencia eliminada',
                    'body'  => 'La ausencia se ha eliminado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'employee-name'     => 'Nombre del empleado',
            'department-name'   => 'Nombre del departamento',
            'time-off-type'     => 'Tipo de ausencia',
            'date'              => 'Fecha',
            'dates'             => 'Fechas',
            'request-date-from' => 'Fecha de solicitud desde',
            'request-date-to'   => 'Fecha de solicitud hasta',
            'description'       => 'Descripción',
            'period'            => 'Período',
            'half-day'          => 'Medio día',
            'requested-days'    => 'Solicitado (días/horas)',
            'attachment'        => 'Adjunto',
            'day'               => ':day día',
            'days'              => ':days día(s)',
            'date-from'         => 'Fecha desde',
            'date-to'           => 'Fecha hasta',
            'status'            => 'Estado',
        ],
    ],
];
