<?php

return [
    'form' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Detalles de la Actividad',

                'fields' => [
                    'activity-type' => 'Tipo de Actividad',
                    'summary'       => 'Resumen',
                    'note'          => 'Nota',
                ],
            ],

            'assignment' => [
                'title' => 'Asignación',

                'fields' => [
                    'assignment' => 'Asignación',
                    'assignee'   => 'Asignado a',
                ],
            ],

            'delay-information' => [
                'title' => 'Información de Retraso',

                'fields' => [
                    'delay-count'          => 'Cantidad de Retraso',
                    'delay-unit'           => 'Unidad de Retraso',
                    'delay-from'           => 'Retraso Desde',
                    'delay-from-helper-text' => 'Origen del cálculo del retraso',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'activity-type' => 'Tipo de Actividad',
            'summary'       => 'Resumen',
            'assignment'    => 'Asignación',
            'assigned-to'   => 'Asignado a',
            'interval'      => 'Intervalo',
            'delay-unit'    => 'Unidad de Retraso',
            'delay-from'    => 'Retraso Desde',
            'created-by'    => 'Creado Por',
            'created-at'    => 'Creado El',
            'updated-at'    => 'Actualizado El',
        ],

        'groups' => [
            'activity-type' => 'Tipo de Actividad',
            'assignment'    => 'Asignación',
            'assigned-to'   => 'Asignado a',
            'interval'      => 'Intervalo',
            'delay-unit'    => 'Unidad de Retraso',
            'delay-from'    => 'Retraso Desde',
            'created-by'    => 'Creado Por',
            'created-at'    => 'Creado El',
            'updated-at'    => 'Actualizado El',
        ],

        'filters' => [
            'activity-type'   => 'Tipo de Actividad',
            'activity-status' => 'Estado de Actividad',
            'has-delay'       => 'Tiene Retraso',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Plantilla de actividad actualizada',
                    'body'  => 'La plantilla de actividad ha sido actualizada exitosamente.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Plantilla de actividad creada',
                    'body'  => 'La plantilla de actividad ha sido creada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Plantilla de actividad eliminada',
                    'body'  => 'La plantilla de actividad ha sido eliminada exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Plantillas de actividad eliminadas',
                    'body'  => 'Las plantillas de actividad han sido eliminadas exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Detalles de la Actividad',

                'entries' => [
                    'activity-type' => 'Tipo de Actividad',
                    'summary'       => 'Resumen',
                ],
            ],

            'assignment' => [
                'title' => 'Asignación',

                'entries' => [
                    'assignment' => 'Asignación',
                    'assignee'   => 'Asignado a',
                ],
            ],

            'delay-information' => [
                'title' => 'Información de Retraso',

                'entries' => [
                    'delay-count'          => 'Cantidad de Retraso',
                    'delay-unit'           => 'Unidad de Retraso',
                    'delay-from'           => 'Retraso Desde',
                    'delay-from-helper-text' => 'Origen del cálculo del retraso',
                ],
            ],
        ],

        'note' => 'Nota',
    ],
];
