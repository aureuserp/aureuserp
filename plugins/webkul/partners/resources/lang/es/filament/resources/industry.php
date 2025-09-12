<?php

return [
    'form' => [
        'name'      => 'Nombre',
        'full-name' => 'Nombre Completo',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'full-name'  => 'Nombre Completo',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Industria actualizada',
                    'body'  => 'La industria ha sido actualizada exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Industria restaurada',
                    'body'  => 'La industria ha sido restaurada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Industria eliminada',
                    'body'  => 'La industria ha sido eliminada exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Industria eliminada permanentemente',
                    'body'  => 'La industria ha sido eliminada permanentemente exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Industrias restauradas',
                    'body'  => 'Las industrias han sido restauradas exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Industrias eliminadas',
                    'body'  => 'Las industrias han sido eliminadas exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Industrias eliminadas permanentemente',
                    'body'  => 'Las industrias han sido eliminadas permanentemente exitosamente.',
                ],
            ],
        ],
    ],
];
