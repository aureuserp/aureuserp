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

            'delete' => [
                'label' => 'Archivar',

                'modal' => [
                    'heading'     => 'Archivar motivo de pérdida',
                    'description' => 'El motivo de pérdida se moverá a la pestaña Archivados. Puede restaurarlo en cualquier momento.',
                ],

                'notification' => [
                    'title' => 'Motivo de pérdida archivado',
                    'body'  => 'El motivo de pérdida se ha archivado correctamente.',
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
