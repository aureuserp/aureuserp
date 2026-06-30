<?php

return [
    'title' => 'Motivo de rechazo',

    'navigation' => [
        'title' => 'Motivos de rechazo',
        'group' => 'Candidaturas',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Nombre',
            'template'         => [
                'title'                    => 'Plantilla',
                'applicant-refuse'         => 'Candidato rechazado',
                'applicant-not-interested' => 'Candidato no interesado',
            ],
            'name-placeholder' => 'Introducir el nombre del motivo de rechazo',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Nombre',
            'template'   => 'Plantilla',
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
                    'title' => 'Motivo de rechazo actualizado',
                    'body'  => 'El motivo de rechazo se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Motivo de rechazo eliminado',
                    'body'  => 'El motivo de rechazo se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Motivos de rechazo eliminados',
                    'body'  => 'Los motivos de rechazo se han eliminado correctamente.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Motivo de rechazo creado',
                    'body'  => 'El motivo de rechazo se ha creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'       => 'Nombre',
        'template'   => 'Plantilla',
    ],
];
