<?php

return [
    'title' => 'Asignación',

    'model-label' => 'Asignación',

    'navigation' => [
        'title' => 'Asignación',
    ],

    'global-search' => [
        'employee'      => 'Empleado',
        'time-off-type' => 'Tipo de ausencia',
        'date-from'     => 'Fecha desde',
        'date-to'       => 'Fecha hasta',
    ],

    'form' => [
        'fields' => [
            'name'                => 'Nombre',
            'name-placeholder'    => 'Tipo de ausencia (desde el inicio de validez hasta el fin de validez/sin límite)',
            'time-off-type'       => 'Tipo de ausencia',
            'employee-name'       => 'Nombre del empleado',
            'allocation-type'     => 'Tipo de asignación',
            'validity-period'     => 'Período de validez',
            'date-from'           => 'Fecha desde',
            'date-to'             => 'Fecha hasta',
            'date-to-placeholder' => 'Sin límite',
            'allocation'          => 'Asignación',
            'allocation-suffix'   => 'Número de días',
            'reason'              => 'Motivo',
        ],
    ],

    'table' => [
        'columns' => [
            'employee-name'   => 'Empleado',
            'time-off-type'   => 'Tipo de ausencia',
            'amount'          => 'Cantidad',
            'allocation-type' => 'Tipo de asignación',
            'status'          => 'Estado',
        ],

        'groups' => [
            'time-off-type'   => 'Tipo de ausencia',
            'employee-name'   => 'Nombre del empleado',
            'allocation-type' => 'Tipo de asignación',
            'status'          => 'Estado',
            'start-date'      => 'Fecha de inicio',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Validar',
                    'approve'  => 'Aprobar',
                ],
                'notification' => [
                    'title' => 'Asignación aprobada',
                    'body'  => 'La asignación se ha aprobado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Asignación eliminada',
                    'body'  => 'La asignación se ha eliminado correctamente.',
                ],
            ],

            'refused' => [
                'title'        => 'Rechazar',
                'notification' => [
                    'title' => 'Asignación rechazada',
                    'body'  => 'La asignación se ha rechazado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Asignaciones eliminadas',
                    'body'  => 'Las asignaciones se han eliminado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'allocation-details' => [
                'title'   => 'Detalles de la asignación',
                'entries' => [
                    'name'                => 'Nombre',
                    'time-off-type'       => 'Tipo de ausencia',
                    'allocation-type'     => 'Tipo de asignación',
                ],
            ],

            'validity-period' => [
                'title'   => 'Período de validez',
                'entries' => [
                    'date-from' => 'Fecha desde',
                    'date-to'   => 'Fecha hasta',
                    'reason'    => 'Motivo',
                ],
            ],
            'allocation-status' => [
                'title'   => 'Estado de la asignación',
                'entries' => [
                    'date-to-placeholder' => 'Sin límite',
                    'allocation'          => 'Número de día(s)',
                    'allocation-value'    => ':days número de días',
                    'state'               => 'Estado',
                ],
            ],
        ],
    ],
];
