<?php

return [
    'heading' => [
        'title' => 'Calendario de mantenimiento',
    ],

    'config' => [
        'button-text' => [
            'today' => 'Hoy',
            'year'  => 'Año',
            'month' => 'Mes',
            'week'  => 'Semana',
            'list'  => 'Lista',
        ],
    ],

    'header-actions' => [
        'create' => [
            'label'         => 'Nueva solicitud',
            'modal-heading' => 'Nueva solicitud de mantenimiento',
            'notification'  => [
                'success' => [
                    'title' => 'Solicitud de mantenimiento creada',
                    'body'  => 'La solicitud de mantenimiento se ha creado correctamente.',
                ],
                'error' => [
                    'title' => 'No se pudo crear la solicitud de mantenimiento',
                    'body'  => 'Cree primero una etapa y un equipo de mantenimiento.',
                ],
            ],
        ],
    ],

    'view-action' => [
        'label' => 'Ver',
    ],

    'modal-actions' => [
        'edit' => [
            'label' => 'Editar',
        ],
    ],

    'form' => [
        'fields' => [
            'subject'      => 'Asunto',
            'scheduled-at' => 'Programado para',
        ],
    ],

    'infolist' => [
        'title'   => 'Solicitud de mantenimiento',
        'entries' => [
            'subject'          => 'Asunto',
            'date'             => 'Fecha',
            'time'             => 'Hora',
            'technician'       => 'Técnico',
            'priority'         => 'Prioridad',
            'maintenance-type' => 'Tipo de mantenimiento',
            'stage'            => 'Etapa',
        ],
    ],
];
