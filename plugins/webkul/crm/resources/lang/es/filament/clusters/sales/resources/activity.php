<?php

return [
    'model-label'        => 'Actividad',
    'plural-model-label' => 'Actividades',

    'navigation' => [
        'title' => 'Mis actividades',
    ],

    'views' => [
        'list'   => 'Lista',
        'kanban' => 'Kanban',
        'graph'  => 'Gráfico',
    ],

    'table' => [
        'columns' => [
            'due-date'      => 'Fecha de vencimiento',
            'opportunity'   => 'Lead / Oportunidad',
            'customer'      => 'Cliente',
            'activity-type' => 'Tipo de actividad',
            'summary'       => 'Resumen',
            'assigned-to'   => 'Asignado a',
            'stage'         => 'Etapa',
            'created-at'    => 'Creado el',
        ],

        'groups' => [
            'activity-type' => 'Tipo de actividad',
            'assigned-to'   => 'Asignado a',
            'due-date'      => 'Fecha de vencimiento',
        ],

        'filters' => [
            'due-status'    => 'Vencimiento',
            'activity-type' => 'Tipo de actividad',
            'assigned-to'   => 'Asignado a',
        ],

        'empty-state' => [
            'heading'     => 'Sin actividades',
            'description' => 'Use Programar actividad, o el icono del reloj en una oportunidad, para planificar su próximo paso.',
        ],
    ],

    'actions' => [
        'schedule' => [
            'label' => 'Programar actividad',

            'modal' => [
                'heading' => 'Programar actividad',
                'submit'  => 'Programar',
            ],

            'form' => [
                'opportunity'   => 'Oportunidad',
                'activity-type' => 'Tipo de actividad',
                'due-date'      => 'Fecha de vencimiento',
                'summary'       => 'Resumen',
                'assigned-to'   => 'Asignado a',
                'note'          => 'Nota',
            ],

            'notification' => [
                'success' => [
                    'title' => 'Actividad programada',
                    'body'  => 'La actividad se ha programado en ":opportunity".',
                ],

                'forbidden' => [
                    'title' => 'No permitido',
                    'body'  => 'No tiene permiso para programar actividades en esta oportunidad.',
                ],
            ],
        ],

        'open' => [
            'label' => 'Abrir registro',
        ],

        'done' => [
            'label'             => 'Marcar como hecha',
            'bulk-notification' => ':count actividad(es) marcada(s) como hecha(s).',

            'modal' => [
                'heading' => 'Marcar actividad como hecha',
                'submit'  => 'Hecho',
            ],

            'form' => [
                'feedback' => 'Comentarios',
            ],

            'notification' => [
                'title' => 'Actividad realizada',
                'body'  => 'La actividad se ha marcado como hecha y se ha registrado en la oportunidad.',
            ],
        ],

        'snooze' => [
            'label'        => 'Posponer :days d',
            'notification' => 'Actividad pospuesta hasta el :date.',
        ],

        'reschedule' => [
            'label' => 'Reprogramar',

            'modal' => [
                'heading' => 'Reprogramar actividad',
            ],

            'form' => [
                'due-date'    => 'Fecha de vencimiento',
                'summary'     => 'Resumen',
                'assigned-to' => 'Asignado a',
            ],

            'notification' => [
                'title' => 'Actividad reprogramada',
                'body'  => 'La actividad se ha actualizado.',
            ],
        ],

        'cancel' => [
            'label'      => 'Cancelar actividad',
            'bulk-label' => 'Cancelar actividades',

            'modal' => [
                'heading' => 'Cancelar actividad',
            ],

            'notification' => [
                'title' => 'Actividad cancelada',
                'body'  => 'La actividad se ha eliminado.',
            ],
        ],
    ],
];
