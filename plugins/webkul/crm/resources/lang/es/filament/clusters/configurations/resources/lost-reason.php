<?php

return [
    'model-label'        => 'Motivo de pérdida',
    'plural-model-label' => 'Motivos de pérdida',

    'navigation' => [
        'title' => 'Motivos de pérdida',
        'group' => 'Pipeline',
    ],

    'form' => [
        'fields' => [
            'name' => 'Descripción',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Descripción',
            'created-by' => 'Creado por',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Motivo de pérdida actualizado',
                    'body'  => 'El motivo de pérdida se ha actualizado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Motivo de pérdida restaurado',
                    'body'  => 'El motivo de pérdida se ha restaurado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Motivo de pérdida eliminado',
                    'body'  => 'El motivo de pérdida se ha eliminado permanentemente.',
                ],
            ],
        ],
    ],
];
