<?php

return [
    'placeholders' => [
        'no-record-found' => 'No se encontró ningún registro.',
        'loading'         => 'Cargando Chatter...',
    ],

    'activity-infolist' => [
        'title' => 'Actividades',
    ],

    'cancel-activity-plan-action' => [
        'title' => 'Cancelar Actividad',
    ],

    'delete-message-action' => [
        'title' => 'Eliminar Mensaje',
    ],

    'edit-activity' => [
        'title' => 'Editar Actividad',

        'form' => [
            'fields' => [
                'activity-plan' => 'Plan de Actividad',
                'plan-date'     => 'Fecha del Plan',
                'plan-summary'  => 'Resumen del Plan',
                'activity-type' => 'Tipo de Actividad',
                'due-date'      => 'Fecha de Vencimiento',
                'summary'       => 'Resumen',
                'assigned-to'   => 'Asignado a',
            ],
        ],

        'action' => [
            'notification' => [
                'success' => [
                    'title' => 'Actividad actualizada',
                    'body'  => 'La actividad ha sido actualizada exitosamente.',
                ],
            ],
        ],
    ],

    'process-message' => [
        'original-note' => '<br><div><span class="font-bold">Nota Original</span>: :body</div>',
        'feedback'      => '<div><span class="font-bold">Comentarios</span>: <p>:feedback</p></div>',
    ],

    'mark-as-done' => [
        'title' => 'Marcar como hecho',
        'form'  => [
            'fields' => [
                'feedback' => 'Comentarios',
            ],
        ],

        'footer-actions' => [
            'label' => 'Hecho y Programar Siguiente',

            'actions' => [
                'notification' => [
                    'mark-as-done' => [
                        'title' => 'Actividad marcada como hecha',
                        'body'  => 'La actividad ha sido marcada como hecha exitosamente.',
                    ],
                ],
            ],
        ],
    ],
];
