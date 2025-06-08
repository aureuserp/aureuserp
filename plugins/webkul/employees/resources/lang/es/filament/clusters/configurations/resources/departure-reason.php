<?php

return [
    'title' => 'Motivos de Baja',

    'navigation' => [
        'title' => 'Motivos de Baja',
        'group' => 'Empleado',
    ],

    'groups' => [
        'status'     => 'Estado',
        'created-by' => 'Creado Por',
        'created-at' => 'Creado El',
        'updated-at' => 'Actualizado El',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nombre',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Nombre',
            'created-by' => 'Creado Por',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'filters' => [
            'name'       => 'Nombre',
            'employee'   => 'Empleado',
            'created-by' => 'Creado Por',
            'updated-at' => 'Actualizado El',
            'created-at' => 'Creado El',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Motivo de baja actualizado',
                    'body'  => 'El motivo de baja ha sido actualizado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Motivo de baja eliminado',
                    'body'  => 'El motivo de baja ha sido eliminado exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Motivos de baja eliminados',
                    'body'  => 'Los motivos de baja han sido eliminados exitosamente.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Motivo de baja creado',
                    'body'  => 'El motivo de baja ha sido creado exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Nombre',
    ],
];
