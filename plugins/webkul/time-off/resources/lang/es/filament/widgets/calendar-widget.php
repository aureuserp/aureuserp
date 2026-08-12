<?php

return [
    'heading' => [
        'title' => 'Solicitudes de ausencia',
    ],

    'modal-actions' => [
        'edit' => [
            'title'                         => 'Editar',
            'duration-display'              => ':count día laborable|:count días laborables',
            'duration-display-with-weekend' => ':count día laborable (+ :weekend día de fin de semana)|:count días laborables (+ :weekend días de fin de semana)',

            'notification' => [
                'title' => 'Ausencia actualizada',
                'body'  => 'Su solicitud de ausencia se ha actualizado correctamente.',
            ],
        ],

        'delete' => [
            'title' => 'Eliminar',
        ],
    ],

    'config' => [
        'button-text' => [
            'today' => 'Hoy',
            'month' => 'Mes',
            'week'  => 'Semana',
            'list'  => 'Lista',
        ],
    ],

    'view-action' => [
        'title'       => 'Ver',
        'description' => 'Ver solicitud de ausencia',
    ],

    'notifications' => [
        'employee-not-found' => [
            'title' => 'Empleado no encontrado',
            'body'  => 'Agregue un empleado a su perfil antes de solicitar una ausencia.',
        ],

        'error' => [
            'title' => 'Algo salió mal',
            'body'  => 'No se pudo procesar su solicitud de ausencia. Inténtelo de nuevo.',
        ],
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

            'success' => [
                'notification' => [
                    'title' => 'Ausencia creada',
                    'body'  => 'Su solicitud de ausencia se ha creado correctamente.',
                ],
            ],
        ],
    ],

    'form' => [
        'title'       => 'Solicitud de ausencia',
        'description' => 'Cree o edite su solicitud de ausencia con los siguientes detalles:',

        'fields' => [
            'time-off-type'             => 'Tipo de ausencia',
            'time-off-type-placeholder' => 'Seleccione un tipo de ausencia',
            'time-off-type-helper'      => 'Seleccione el tipo de ausencia que está solicitando.',
            'request-date-from'         => 'Fecha de solicitud desde',
            'request-date-to'           => 'Fecha de solicitud hasta',
            'period'                    => 'Período',
            'half-day'                  => 'Medio día',
            'half-day-helper'           => 'Active para una ausencia de medio día.',
            'requested-days'            => 'Solicitado (días/horas)',
            'description'               => 'Descripción',
            'description-placeholder'   => 'Sin descripción proporcionada',
            'description-helper'        => 'Proporcione una breve descripción de su solicitud de ausencia.',
            'duration'                  => 'Duración',
            'please-select-dates'       => 'Seleccione la fecha de solicitud desde y hasta.',
        ],
    ],

    'infolist' => [
        'title'       => 'Detalles de la ausencia',
        'description' => 'Estos son los detalles de su solicitud de ausencia:',
        'entries'     => [
            'time-off-type'           => 'Tipo de ausencia',
            'request-date-from'       => 'Fecha de solicitud desde',
            'request-date-to'         => 'Fecha de solicitud hasta',
            'description'             => 'Descripción',
            'description-placeholder' => 'Sin descripción proporcionada',
            'duration'                => 'Duración',
            'status'                  => 'Estado',
        ],
    ],

    'events' => [
        'title' => ':name en :status: :days día(s)',
    ],
];
