<?php

return [
    'title' => 'Motivos de baja',

    'navigation' => [
        'title' => 'Motivos de baja',
        'group' => 'Empleado',
    ],

    'groups' => [
        'status'     => 'Estado',
        'created-by' => 'Creado por',
        'created-at' => 'Creado el',
        'updated-at' => 'Actualizado el',
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
            'created-by' => 'Creado por',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'filters' => [
            'name'       => 'Nombre',
            'employee'   => 'Empleado',
            'created-by' => 'Creado por',
            'updated-at' => 'Actualizado el',
            'created-at' => 'Creado el',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Motivo de baja actualizado',
                    'body'  => 'El motivo de baja se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Motivo de baja eliminado',
                    'body'  => 'El motivo de baja se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Motivos de baja eliminados',
                    'body'  => 'Los motivos de baja se han eliminado correctamente.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Motivo de baja creado',
                    'body'  => 'El motivo de baja se ha creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Nombre',
    ],
];
