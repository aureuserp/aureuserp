<?php

return [
    'heading' => [
        'title' => 'Resumen de ausencias',
    ],

    'modal-actions' => [
        'edit' => [
            'title'        => 'Editar',
            'notification' => [
                'title' => 'Ausencia actualizada',
                'body'  => 'La solicitud de ausencia se ha actualizado correctamente.',
            ],
        ],

        'delete' => [
            'title' => 'Eliminar',
        ],
    ],

    'view-action' => [
        'title'       => 'Ver',
        'description' => 'Ver solicitud de ausencia',
    ],

    'header-actions' => [
        'create' => [
            'title'       => 'Nueva ausencia',
            'description' => 'Crear solicitud de ausencia',

            'notification' => [
                'title' => 'Ausencia creada',
                'body'  => 'La solicitud de ausencia se ha creado correctamente.',
            ],

            'employee-not-found' => [
                'notification' => [
                    'title' => 'Empleado no encontrado',
                    'body'  => 'Agregue un empleado a su perfil antes de crear una solicitud de ausencia.',
                ],
            ],
        ],
    ],

    'form' => [
        'fields' => [
            'time-off-type'     => 'Tipo de ausencia',
            'request-date-from' => 'Fecha de solicitud desde',
            'request-date-to'   => 'Fecha de solicitud hasta',
            'period'            => 'Período',
            'half-day'          => 'Medio día',
            'requested-days'    => 'Solicitado (días/horas)',
            'description'       => 'Descripción',
        ],
    ],

    'infolist' => [
        'entries' => [
            'time-off-type'           => 'Tipo de ausencia',
            'request-date-from'       => 'Fecha de solicitud desde',
            'request-date-to'         => 'Fecha de solicitud hasta',
            'description'             => 'Descripción',
            'description-placeholder' => 'Sin descripción proporcionada',
            'duration'                => 'Duración',
            'status'                  => 'Estado',
        ],
    ],
];
