<?php

return [
    'heading' => 'Chatter',

    'placeholders' => [
        'no-record-found' => 'No se encontró ningún registro.',
        'loading'         => 'Cargando Chatter...',
    ],

    'activity-infolist' => [
        'title' => 'Actividades',
    ],

    'cancel-activity-plan-action' => [
        'title' => 'Cancelar actividad',
    ],

    'delete-message-action' => [
        'title' => 'Eliminar mensaje',
    ],

    'edit-activity' => [
        'title' => 'Editar actividad',

        'form' => [
            'fields' => [
                'activity-plan' => 'Plan de actividad',
                'plan-date'     => 'Fecha del plan',
                'plan-summary'  => 'Resumen del plan',
                'activity-type' => 'Tipo de actividad',
                'due-date'      => 'Fecha de vencimiento',
                'summary'       => 'Resumen',
                'assigned-to'   => 'Asignado a',
            ],
        ],

        'action' => [
            'notification' => [
                'success' => [
                    'title' => 'Actividad actualizada',
                    'body'  => 'La actividad se ha actualizado correctamente.',
                ],
            ],
        ],
    ],

    'process-message' => [
        'original-note' => '<br><div><span class="font-bold">Nota original</span>: :body</div>',
        'original-note' => '<br><div><span class="font-bold">Nota original</span>: :body</div>',
        'feedback'      => '<div><span class="font-bold">Comentarios</span>: <p>:feedback</p></div>',
    ],

    'mark-as-done' => [
        'title' => 'Marcar como hecho',
        'actions' => [
            'done' => [
                'label' => 'Hecho',
            ],
        ],
        'form'  => [
            'fields' => [
                'feedback' => 'Comentarios',
            ],
        ],

        'footer-actions' => [
            'label' => 'Hecho y programar siguiente',

            'actions' => [
                'notification' => [
                    'mark-as-done' => [
                        'title' => 'Actividad marcada como hecha',
                        'body'  => 'La actividad se ha marcado como hecha correctamente.',
                    ],
                ],
            ],
        ],
    ],
];
